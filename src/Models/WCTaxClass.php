<?php


namespace JB000\WooCommerce\Models;


use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCTaxClass
 *
 * @package JB000\WooCommerce\Models
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