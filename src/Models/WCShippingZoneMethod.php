<?php


namespace JB000\WooCommerce\Models;


use Illuminate\Support\Collection;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCShippingZone
 *
 * @package JB000\WooCommerce\Models
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