<?php


namespace JB000\WooCommerce\Models;


use Automattic\WooCommerce\Client;
use ArrayAccess;
use Illuminate\Database\Eloquent\Concerns;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Queue\QueueableEntity;


abstract class BaseModel implements ArrayAccess, Arrayable, Jsonable, JsonSerializable, QueueableEntity, UrlRoutable
{
	use Concerns\HasAttributes,
		Concerns\HasEvents,
		Concerns\HasGlobalScopes,
		Concerns\HasRelationships,
		Concerns\HasTimestamps,
		Concerns\HidesAttributes,
		Concerns\GuardsAttributes;



	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';


	/**
	 * Indicates if the model exists.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * Indicates if the model was inserted during the current request lifecycle.
	 *
	 * @var bool
	 */
	public $wasRecentlyCreated = false;


    /**
     * @var bool
     */
    protected $incrementing = false;


	/**
	 * The array of booted models.
	 *
	 * @var array
	 */
	protected static $booted = [];


    /**
     * @var Client
     */
    protected static $client;


	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'date_created';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'date_modified';

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 */
	public function __construct(array $attributes = [])
	{
		$this->bootIfNotBooted();

		$this->syncOriginal();

		$this->fill($attributes);
	}

	/**
	 * Check if the model needs to be booted and if so, do it.
	 *
	 * @return void
	 */
	protected function bootIfNotBooted()
	{
		if (! isset(static::$booted[static::class])) {
			static::$booted[static::class] = true;

			$this->fireModelEvent('booting', false);

			static::boot();

			$this->fireModelEvent('booted', false);
		}
	}

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		static::bootTraits();
	}


	public static function setClient(Client $client)
    {
        static::$client = $client;
    }

    public static function getClient()
    {
        return static::$client;
    }

	/**
	 * Boot all of the bootable traits on the model.
	 *
	 * @return void
	 */
	protected static function bootTraits()
	{
		$class = static::class;

		foreach (class_uses_recursive($class) as $trait) {
			if (method_exists($class, $method = 'boot'.class_basename($trait))) {
				forward_static_call([$class, $method]);
			}
		}
	}

	/**
	 * Clear the list of booted models so they will be re-booted.
	 *
	 * @return void
	 */
	public static function clearBootedModels()
	{
		static::$booted = [];
	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @return $this
	 *
	 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function fill(array $attributes)
	{
		$totallyGuarded = $this->totallyGuarded();

		foreach ($this->fillableFromArray($attributes) as $key => $value) {
			$key = $this->removeTableFromKey($key);

			// The developers may choose to place some attributes in the "fillable" array
			// which means only those attributes may be set through mass assignment to
			// the model, and all others will just get ignored for security reasons.
			if ($this->isFillable($key)) {
				$this->setAttribute($key, $value);
			} elseif ($totallyGuarded) {
				throw new MassAssignmentException($key);
			}
		}

		return $this;
	}

	/**
	 * Fill the model with an array of attributes. Force mass assignment.
	 *
	 * @param  array  $attributes
	 * @return $this
	 */
	public function forceFill(array $attributes)
	{
		return static::unguarded(function () use ($attributes) {
			return $this->fill($attributes);
		});
	}

	/**
	 * Remove the table name from a given key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function removeTableFromKey($key)
	{
		return Str::contains($key, '.') ? last(explode('.', $key)) : $key;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool  $exists
	 * @return static
	 */
	public function newInstance($attributes = [], $exists = false)
	{
		// This method just provides a convenient way for us to generate fresh model
		// instances of this current model. It is particularly useful during the
		// hydration of new objects via the Eloquent query builder instances.
		$model = new static((array) $attributes);

		$model->exists = $exists;

		return $model;
	}

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @return static
	 */
	public function newFromBuilder($attributes = [])
	{
		$model = $this->newInstance([], true);

		$model->setRawAttributes((array) $attributes, true);

		return $model;
	}


    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }



	/**
	 * Get the primary key value for a save query.
	 *
	 * @return mixed
	 */
	protected function getKeyForSaveQuery()
	{
		return isset($this->original[$this->getKeyName()])
			? $this->original[$this->getKeyName()]
			: $this->getAttribute($this->getKeyName());
	}



	/**
	 * Destroy the models for the given IDs.
	 *
	 * @param  array|int  $ids
	 * @return int
	 */
	public static function destroy($ids)
	{
		// We'll initialize a count here so we will return the total number of deletes
		// for the operation. The developers can then check this number as a boolean
		// type value or get this total count of records deleted for logging, etc.
		$count = 0;

		$ids = is_array($ids) ? $ids : func_get_args();

		// We will actually pull the models from the database table and call delete on
		// each of them individually so that their events get fired properly with a
		// correct set of attributes in case the developers wants to check these.
		$key = with($instance = new static)->getKeyName();

		foreach ($instance->whereIn($key, $ids)->get() as $model) {
			if (method_exists($model,'delete') && $model->delete())
			{
				$count++;
			}
		}

		return $count;
	}



	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_merge($this->attributesToArray(), $this->relationsToArray());
	}

	/**
	 * Convert the model instance to JSON.
	 *
	 * @param  int  $options
	 * @return string
	 *
	 * @throws \Illuminate\Database\Eloquent\JsonEncodingException
	 */
	public function toJson($options = 0)
	{
		$json = json_encode($this->jsonSerialize(), $options);

		if (JSON_ERROR_NONE !== json_last_error()) {
			throw JsonEncodingException::forModel($this, json_last_error_msg());
		}

		return $json;
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}


	/**
	 * Get the primary key for the model.
	 *
	 * @return string
	 */
	public function getKeyName()
	{
		return $this->primaryKey;
	}

	/**
	 * Set the primary key for the model.
	 *
	 * @param  string  $key
	 * @return $this
	 */
	public function setKeyName($key)
	{
		$this->primaryKey = $key;
		return $this;
	}

	/**
	 * Get the value of the model's primary key.
	 *
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->getAttribute($this->getKeyName());
	}

	/**
	 * Get the queueable identity for the entity.
	 *
	 * @return mixed
	 */
	public function getQueueableId()
	{
		return $this->getKey();
	}

	/**
	 * Get the value of the model's route key.
	 *
	 * @return mixed
	 */
	public function getRouteKey()
	{
		return $this->getAttribute($this->getRouteKeyName());
	}

	/**
	 * Get the route key for the model.
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
		return $this->getKeyName();
	}

	/**
	 * Get the default foreign key name for the model.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return Str::snake(class_basename($this)).'_'.$this->primaryKey;
	}


	/**
	 * Dynamically retrieve attributes on the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{

		return $this->getAttribute($key);
	}

	/**
	 * Dynamically set attributes on the model.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->setAttribute($key, $value);
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param  mixed  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * Set the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
	}

	/**
	 * Unset the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}

	/**
	 * Determine if an attribute or relation exists on the model.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return ! is_null($this->getAttribute($key));
	}

	/**
	 * Unset an attribute on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key], $this->relations[$key]);
	}

	/**
	 * Handle dynamic method calls into the model.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, ['increment', 'decrement'])) {
			return $this->$method(...$parameters);
		}

		return $this->newQuery()->$method(...$parameters);
	}

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return (new static)->$method(...$parameters);
	}

	/**
	 * Convert the model to its string representation.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * When a model is being unserialized, check if it needs to be booted.
	 *
	 * @return void
	 */
	public function __wakeup()
	{
		$this->bootIfNotBooted();
	}
}
