<?php

namespace SamParish\WooCommerce\Models;
use Illuminate\Support\Collection;


/**
 * Class WCShippingLineItem
 *
 * @package SamParish\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property string $methodTitle
 * @property string $methodId
 * @property double $total
 * @property-read double $totalTax
 * @property-read Collection $taxes
 * @property-read Collection $metaData
 */
class WCShippingLineItem extends  Model
{

    protected $casts = [
        'total' => 'double',
        'total_tax' => 'double',
        'taxes' => [WCTax::class],
        'meta_data' => [WCMeta::class]
    ];
}