<?php

namespace JB000\WooCommerce\Models;


/**
 * Class WCTaxLineItem
 * @package JB000\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property-read string $rateCode
 * @property-read string $rateId
 * @property-read string $label
 * @property-read bool $compound
 * @property-read double $taxTotal
 * @property-read double $shippingTaxTotal
 */
class WCTaxLineItem extends Model
{

    protected $casts = [
        'compound' => 'bool',
        'tax_total' => 'double',
        'shipping_tax_total' => 'double',
    ];
}