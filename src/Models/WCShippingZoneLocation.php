<?php


namespace SamParish\WooCommerce\Models;


use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCShippingZone
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property string $code
 * @property string $type
 */
class WCShippingZoneLocation extends Model
{
    protected $endpoint = 'shipping/zones/{parentId}/locations';

	protected $paginate = false;

    protected $casts = [
        'order' => 'int'
    ];


}