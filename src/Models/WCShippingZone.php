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
     * @return \SamParish\WooCommerce\Builders\Builder
     */
    public function locations()
    {
        return $this->hasChildren(WCShippingZoneLocation::class);
    }

    /**
     * @return \SamParish\WooCommerce\Builders\Builder
     */
    public function methods()
    {
        return $this->hasChildren(WCShippingZoneMethod::class);
    }
}