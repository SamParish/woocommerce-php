<?php

namespace JB000\WooCommerce\Models;
use Illuminate\Support\Collection;


/**
 * Class WCCouponLineItem
 *
 * @package JB000\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property string $code
 * @property double $discount
 * @property double $discountTax
 * @property-read Collection $metaData
 */
class WCCouponLineItem extends  Model
{
    protected $casts = [
        'discount' => 'double',
        'discount_tax' => 'double',
        'meta_data' => [WCMeta::class],
    ];

}