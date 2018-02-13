<?php


namespace JB000\WooCommerce\Models;


/**
 * Class WCProductAttribute
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property int $position
 * @property bool $visible
 * @property bool $variation
 * @property array $options
 */
class WCProductAttribute extends Model
{
    protected $casts = [
       'int' => 'position',
       'visible' => 'bool',
       'variation' => 'bool',
    ];

}