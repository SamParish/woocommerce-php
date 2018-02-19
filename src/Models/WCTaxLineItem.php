<?php

namespace SamParish\WooCommerce\Models;


/**
 * Class WCTaxLineItem
 * @package SamParish\WooooProcessor\Models
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