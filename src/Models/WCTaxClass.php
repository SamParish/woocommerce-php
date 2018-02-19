<?php


namespace SamParish\WooCommerce\Models;


use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCTaxClass
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property-read string $slug
 * @property string $name
 */
class WCTaxClass extends Model
{

    use CanDelete,
        CanSave;

    protected $endpoint = 'taxes/classes';


    protected function beforeSave()
    {
        if($this->exists)
            throw new \Exception("API does not support updating this entity.");

    }
}