<?php


namespace SamParish\WooCommerce\Traits;


use SamParish\WooCommerce\Models\Model;

trait CanDelete
{
    /**
     * Delete the model from the database.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function delete()
    {
        if (is_null($this->getKeyName())) {
            throw new \Exception('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (! $this->exists) {
            return false;
        }

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->performDelete();

        $this->exists = false;

        // Once the model has been deleted, we will fire off the deleted event so that
        // the developers may hook into post-delete operations. We will then return
        // a boolean true as the delete is presumably successful on the database.
        $this->fireModelEvent('deleted', false);

        return true;
    }


    protected function performDelete()
    {
        $endpoint = $this->getEndpoint();
        return Model::getClient()->delete($endpoint);
    }
}