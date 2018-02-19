<?php


namespace SamParish\WooCommerce\Models;


use Illuminate\Support\Collection;
use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCShippingZone
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property-read int $id
 * @property-read string $title
 * @property integer $order
 * @property bool $enabled
 * @property string $methodId
 * @property string $methodTitle
 * @property string $methodDescription
 * @property-read Collection $settings
 */
class WCShippingZoneMethod extends Model
{
    use CanSave, CanDelete;

    protected $endpoint = 'shipping/zones/{parentId}/methods';

	protected $paginate = false;

    protected $casts = [
        'id' => 'int',
        'order' => 'int',
        'enabled' => 'bool',
        'settings' => [WCShippingZoneMethodSetting::class],
    ];


}