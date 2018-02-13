<?php


namespace JB000\WooCommerce\Builders;

use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Models\Model;

class Builder
{
    protected $params = [];

    protected $paginate = false;

    protected $endpoint = "";

    protected $perPage = 15;

    protected $relations = [];

    private $class;

    private $nextPageAvailable = true;


    public function __construct($class)
    {

        $this->class = $class;

        //create a new class instance to get properties
        $instance = new $class;

        $this->endpoint = $instance->getEndpoint();
        $this->perPage = $instance->getPerPage();
	    $this->paginate = $instance->getPaginate();
        unset($instance);

        $this->limit($this->perPage);
    }


    /**
     * Filter by context
     *
     * Options: view,edit
     *
     * @param string $context
     * @return $this
     */
    public function context($context='view')
    {
        $this->params['context'] = $context;
        return $this;
    }

	/**
	 * Skips queried entities by the amount supplied
	 *
	 * @param $amount
	 * @return $this
	 */
    public function skip($amount)
    {
        $this->params['offset'] = $amount;
	    return $this;
    }

    /**
     * Limits the amount of entities per page.
     *
     * @param $amount
     * @return $this
     */
    public function limit($amount)
    {
        $this->params['per_page'] = $amount;
        return $this;
    }

    /**
     * @param $page
     * @return $this
     */
    public function page($page)
    {
        $this->params['page'] = $page;
        return $this;
    }

    /**
     * Gets the next page of results
     *
     * @return Collection
     */
    public function next()
    {
        //increment page by one.
        $currentPage = $this->params['page'] ?? 1;
        $this->page($currentPage+1);
        $results = $this->get();

        //if results equals the page limit, then we assume we have some more results
        $this->nextPageAvailable = ($results->count() == $this->params['per_page']);

        return $results;
    }

    /**
     * Run the query and get the results.
     *
     * @return Collection
     */
    public function get()
    {
        $this->validateEndpoint();
        $result = Model::getClient()->get($this->getEndpoint(),$this->params);
        return $this->hydrate($result);
    }


    /**
     * @throws \Exception
     */
    protected function validateEndpoint()
    {
        if(!$this->getEndpoint())
            throw new \Exception("Endpoint is not valid");
    }


    /**
     * Gets all the results
     *
     * @return Collection
     */
    public function all()
    {
        //if we cannot paginate, then simply return the response from get
        if(!$this->paginate)
            return $this->get();


        $this->page(0);
        $results = new Collection();
        while($this->nextPageAvailable)
        {
            foreach($this->next() as $item)
                $results->push($item);
        }
        return $results;
    }


	/**
	 * Gets all the results however will call the supplied callback after each request is made.
	 *
	 * @param mixed $callback
	 * @param int $amount
	 *
	 * @throws \Exception
	 */
	public function chunk($amount,$callback)
	{
		//we can only chunk if we can paginate the result
		if(!$this->paginate)
			throw new \Exception("Unable to chunk model as pagination is not supported");

		//set page to the start
		$this->page(0);

		//if we have an amount supplied then set the page limit now
		$this->limit($amount);

		//it is wise to order by ID so we have a set order
		$this->orderBy('id');

		while($this->nextPageAvailable)
		{
			//get the page and send the results to the callback supplied
			$results = $this->next();
			call_user_func($callback,$results);
			unset($results);
		}
	}


    /**
     *
     * Queries the builder and returns the first item
     *
     * @return mixed|null
     */
    public function first()
    {
        //if we can paginate, then we can set the query to just return 1
        if($this->paginate)
        {
            $results = $this->limit(1)
                ->page(1)
                ->get();
        }
        else
        {
            $results = $this->get();
        }


        if(count($results)>0)
            return $results[0];

        return null;
    }


    /**
     * @param $id
     * @return mixed|null
     * @throws \Exception
     */
    public function find($id)
    {
        $this->validateEndpoint();

        try
        {

            $endpoint = $this->getEndpoint().$id;
            $response = Model::getClient()->get($endpoint, $this->params);
            return $this->newFromBuilder($response);
        }
        catch (HttpClientException $ex)
        {
	        if($ex->getCode() == 404)
		        return null;

	        throw $ex;
        }
    }


    /**
     * @return string
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
     * Creates a new entity with the same relationships as the builder.
     *
     * @return mixed
     */
    public function create()
    {
        $instance = new $this->class;
        $instance->setRelations($this->relations);
        return $instance;

    }

    public function setRelation($key,$value)
    {
        $this->relations[$key] = $value;
    }

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @param $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOrFail($id)
    {
        $order =  $this->find($id);
        if(!$order)
            throw new ModelNotFoundException("$id does not exist");

        return $order;
    }


    /**
     * @param $attributes
     * @return mixed
     */
    public function newFromBuilder($attributes)
    {
        $instance = (new $this->class)->newFromBuilder($attributes);
        foreach($this->relations as $k=>$v)
            $instance->setRelation($k,$v);

        return $instance;
    }

    /**
     * Transforms an array of models into the cast type.
     *
     * @param array $models
     * @return Collection
     */
    public function hydrate(array $models)
    {
        $collection = new Collection();
        foreach($models as $model)
        {
            $collection->push($this->newFromBuilder($model));
        }
        return $collection;
    }


    /**
     * Orders the query in Ascending order
     * @return $this
     */
    public function orderAsc()
    {
        $this->params['order'] = 'asc';
        return $this;
    }


    /**
     * Orders the query in Descending order
     * @return $this
     */
    public function orderDesc()
    {
        $this->params['order'] = 'desc';
        return $this;
    }


    /**
     * @param $key
     * @return $this
     */
    public function orderBy($key)
    {
        $this->params['orderby'] = $key;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function where($key,$value)
    {
        $this->params[$key] = $value;
        return $this;
    }





}
