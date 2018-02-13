<?php


namespace JB000\WooCommerce\Models;
use Illuminate\Support\Collection;


/**
 * Class WCFeeLineItem
 *
 * @package JB000\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property string $name
 * @property string $taxClass
 * @property string $taxStatus
 * @property double $total
 * @property-read double $totalTax
 * @property-read Collection $taxes
 * @property-read Collection $metaData
 */
class WCFeeLineItem extends Model
{

    protected $casts = [
        'total' => 'double',
        'total_tax' => 'double',
        'taxes' => [WCTax::class],
        'meta_data' => [WCMeta::class],
    ];

}