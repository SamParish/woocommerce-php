<?php


namespace JB000\WooCommerce\Models;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;


/**
 * Class WCTaxRate
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $country
 * @property string $state
 * @property string $postcode
 * @property string $city
 * @property string $rate
 * @property string $name
 * @property int $priority
 * @property bool $compound
 * @property bool $shipping
 * @property int $order
 * @property string $class
 */
class WCTaxRate extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'taxes';

	protected $paginate = true;

    protected $casts = [
        'priority' => 'int',
        'compound' => 'bool',
        'shipping' => 'bool',
        'int' => 'order'
    ];
}