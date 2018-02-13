<?php


namespace JB000\WooCommerce\Models;


use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCShippingZone
 *
 * @package JB000\WooCommerce\Models
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