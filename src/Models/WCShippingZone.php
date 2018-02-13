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
 * @property string $name
 * @property int $order
 * @property-read Collection $locations
 * @property-read Collection $methods
 */
class WCShippingZone extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'shipping/zones';

	protected $paginate = false;

    protected $casts = [
        'order' => 'int'
    ];


    /**
     * @return \JB000\WooCommerce\Builders\Builder
     */
    public function locations()
    {
        return $this->hasChildren(WCShippingZoneLocation::class);
    }

    /**
     * @return \JB000\WooCommerce\Builders\Builder
     */
    public function methods()
    {
        return $this->hasChildren(WCShippingZoneMethod::class);
    }
}