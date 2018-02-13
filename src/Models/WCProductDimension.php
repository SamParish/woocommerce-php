<?php

namespace JB000\WooCommerce\Models;

/**
 * Class WCDimension
 * @package JB000\WooCommerce\Models
 *
 * @property double $length
 * @property double $width
 * @property double $height
 */
class WCProductDimension extends Model
{

    protected $casts = [
        'length' => 'double',
        'width' => 'double',
        'height' => 'double'
    ];
}