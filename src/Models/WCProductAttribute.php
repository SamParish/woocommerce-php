<?php


namespace SamParish\WooCommerce\Models;


/**
 * Class WCProductAttribute
 *
 * @package SamParish\WooCommerce\Models
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