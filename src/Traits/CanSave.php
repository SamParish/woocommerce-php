<?php


namespace JB000\WooCommerce\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Models\Model;

trait CanSave
{

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        //transform attributes to array
        $this->castsToArray();


        // If we have a parent relationship, then the parent must be saved first
        if(array_key_exists('parent',$this->getRelations()) && !$this->getRelation('parent')->exists)
            throw new \Exception("Parent must first be saved");


        //call the on save method
        if($this->beforeSave() === false)
            return false;


        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }


        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists)
        {
            $saved = $this->isDirty() ? $this->update() : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->insert();
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done.
        if ($saved) {

            $this->fireModelEvent('saved', false);

            $this->syncOriginal();

            if (Arr::get($options, 'touch', true)) {
                $this->touchOwners();
            }
        }

        $this->afterSave($saved);

        return $saved;
    }


    protected function beforeSave() {}

    protected function afterSave($saved) {}




    /**
     * @return array
     */
    protected function getAvailableCasts()
    {
        if(property_exists($this,'availableCasts') && is_array($this->availableCasts))
            return $this->availableCasts;

        return [];
    }

    /**
     * Method called before save
     */
    public function castsToArray()
    {
        //ensure we have available casts
        if(!property_exists($this,'availableCasts') || !is_array($this->getAvailableCasts()))
            return;


        foreach($this->attributes as $key=>$value)
        {
            //only interested in objects
            if(!is_object($value))
                continue;



            if (get_class($value) == Collection::class)
            {
                //we have a collection

                $attributes = [];
                foreach($value as $_value)
                {
                    if(in_array(get_class($_value),$this->getAvailableCasts()))
                        $attributes[] = $_value->attributes;
                }
                $this->attributes[$key] = $attributes;


                if(in_array(get_class($value),$this->getAvailableCasts()))
                    $this->attributes[$key] = $value->attributes;

            }
            else
            {
                if(in_array(get_class($value),$this->getAvailableCasts()))
                    $this->attributes[$key] = $value->attributes;
            }
        }
    }


    /**
     * Perform a model update operation.
     *
     * @return bool
     */
    protected function update()
    {

        // If the updating event returns false, we will cancel the update operation so
        // developers can hook Validation systems into their models and cancel this
        // operation if the model does not pass validation. Otherwise, we update.
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }


        // Once we have run the update operation, we will fire the "updated" event for
        // this model instance. This will allow developers to hook into these after
        // models are updated, giving them a chance to do any special processing.
        $dirty = $this->getDirty();


        if (count($dirty) > 0)
        {
            $this->setRawAttributes($this->performUpdate($dirty));

            $this->fireModelEvent('updated', false);
        }

        return true;
    }


    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function insert()
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }


        // Get the attributes and check to see if they are empty
        $attributes = $this->attributes;
        if (empty($attributes)) {
            return true;
        }

        $this->setRawAttributes($this->performInsert($attributes));


        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created', false);

        return true;
    }

    protected function performInsert($attributes = [])
    {
        $endpoint = $this->getEndpoint();
        return Model::getClient()->post($endpoint,$attributes);
    }
    protected function performUpdate($attributes = [])
    {
        $endpoint = $this->getEndpoint().$this->getKey();
        return Model::getClient()->put($endpoint,$attributes);
    }


}