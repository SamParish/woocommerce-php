<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 14/07/2016
 * Time: 09:59
 */

namespace JB000\WooCommerce\Models;


/**
 * Class WCLineItemTax
 * @package JB000\WooooProcessor\Models
 *
 * @property-read string $id
 * @property-read double $total
 * @property-read double $subtotal
 */
class WCTax extends Model
{
    protected $casts = [
        'total' => 'double',
        'subtotal' => 'double'
    ];
}