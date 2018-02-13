<?php

namespace JB000\WooCommerce\Models;

use Carbon\Carbon;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Builders\Builder;


abstract class Model extends BaseModel
{
    use CamelCasing;

    const DATE_FORMAT_ISO8601 = 'Y-m-d\TH:i:s';

	/**
	 * @var string
	 */
	protected $dateFormat = self::DATE_FORMAT_ISO8601;

    protected $perPage = 10;

    protected $endpoint;

	protected $paginate = false;

    protected $availableCasts = [
        WCAddress::class,
        WCLineItem::class,
        WCTaxLineItem::class,
        WCShippingLineItem::class,
        WCFeeLineItem::class,
        WCCouponLineItem::class,
        WCPaymentGatewaySetting::class,
        WCProductAttribute::class,
        WCProductVariationAttribute::class,
        WCProductCategory::class,
        WCProductDefaultAttribute::class,
        WCProductDimension::class,
        WCProductDownload::class,
        WCProductTag::class,
        WCImage::class,
        WCMeta::class,
        WCTax::class,
        WCTag::class,
    ];

    public function __construct(array $attributes = [])
    {
        //set defaults
        foreach($this->casts as $key=>$value)
        {
            if(is_array($value))
            {
                $this->setAttribute($key,[]);
            }
            else
            {
                if(in_array($value,$this->availableCasts))
                    $this->setAttribute($key,new $value);
            }
        }

        parent::__construct($attributes);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * We have to override this method as the original does not work with the format returned from WooCommerce
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        return new Carbon($value);
    }


    /**
     * @param array $value
     * @param string $type
     * @return null|mixed
     */
    protected function castType($value,$type)
    {
        //ensure we can cast
        $instance = new $type;
        if(method_exists($instance,'newFromBuilder') && is_array($value))
            return $instance->newFromBuilder($value);

        return null;
    }

    /**
     * Overridden method to account for additional cast types.
     *
     * Will only cast if the original attribute is an array.
     *
     * Once cast it will set the original attribute to the new value so properties can be modified.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {


        //we are only working with arrays here
        if(!is_array($value))
            return parent::castAttribute($key,$value);


        $type = $this->getCastType($key);

        //ensure type is valid
        if((is_array($type) && !in_array($type[0],$this->availableCasts)) && !in_array($type,$this->availableCasts))
            return parent::castAttribute($key,$value);



        //if the type is an array, this infers that we must cast to a collection
        if(is_array($type))
        {
            $type = $type[0];
            $castedAttribute = new Collection();
            foreach($value as $_value)
            {
                $castedAttribute->push($this->castType($_value,$type));
            }
        }
        else
        {
            $castedAttribute = $this->castType($value,$type);
        }

        $this->setAttribute($key, $castedAttribute);
        return $castedAttribute;

    }

    /**
     * overrides method to add support for casts supplied in array format.
     *
     * @param  string  $key
     * @return string|array
     */
    protected function getCastType($key)
    {
        $val = $this->getCasts()[$key];

        if((is_array($val) && in_array($val[0],$this->availableCasts)) || in_array($val,$this->availableCasts))
            return $val;

        return trim(strtolower($val));
    }

    /**
     * Get a relationship value from a method.
     *
     * @param  string  $method
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getRelationshipFromMethod($method)
    {
        $relation = $this->$method();

        if (! $relation instanceof Builder) {
            throw new \Exception('Relationship method must return an object of type '.Builder::class);
        }

        return tap($relation->get(), function ($results) use ($method) {
            $this->setRelation($method, $results);
        });
    }


    /**
     * @param $class
     * @return Builder
     */
    public function hasChildren($class)
    {
        $builder = new Builder($class);
        $builder->setRelation('parent',$this);
        return $builder;
    }

    /**
     * Gets the endpoint
     *
     * @return string|null
     */
    public function getEndpoint()
    {
        $endpoint = $this->endpoint;
        if(!ends_with($this->endpoint,'/'))
            $endpoint .='/';

        //replace {parentId} with the primary id from the parent relationship (if we have one)
        if(str_contains($endpoint,'{parentId}') && array_key_exists('parent',$this->relations))
        {
            $parentId = $this->relations['parent']->getKey();
            $endpoint = str_replace('{parentId}',$parentId,$endpoint);
        }
        return $endpoint;

    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

	/**
	 * @return bool
	 */
	public function getPaginate()
	{
		return $this->paginate;
	}

    /**
     * @return Builder
     * @throws \Exception
     */
    public function newQuery()
    {
        if(!$this->endpoint)
        {
            $className = get_class($this);
            throw new \Exception("$className does not have a valid endpoint");
        }
        $builder = new Builder(get_class($this));
        return $builder;
    }

}