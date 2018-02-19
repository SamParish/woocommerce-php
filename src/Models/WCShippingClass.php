<?php


namespace SamParish\WooCommerce\Models;


use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCShippingClass
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $count
 */
class WCShippingClass extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'products/shipping_classes';

	protected $paginate = true;

    protected $casts = [
        'count' => 'int'
    ];
}