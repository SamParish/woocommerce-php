<?php

namespace SamParish\WooCommerce\Models;
use Illuminate\Support\Collection;
use SamParish\WooCommerce\Traits\HasMeta;

/**
 * Class WCLineItem
 * @package SamParish\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property string $name
 * @property-read string $sku
 * @property integer $productId
 * @property integer $variationId
 * @property integer $quantity
 * @property string $taxClass
 * @property-read double $price
 * @property double $subtotal
 * @property-read double $subtotalTax
 * @property double $total
 * @property-read double $totalTax
 * @property-read Collection $taxes
 * @property-read Collection $metaData
 */
class WCLineItem extends Model
{

	use HasMeta;

    protected $casts =[
        'product_id' => 'int',
        'variation_id' => 'int',
        'quantity' => 'int',
        'price' => 'double',
        'subtotal' => 'double',
        'subtotal_tax' => 'double',
        'total' => 'double',
        'total_tax' => 'double',
        'taxes' => [WCTax::class],
        'meta_data' => [WCMeta::class],
    ];


    /**
     * Checks to see if the SKU has been set
     *
     * @return bool
     */
    public function skuExists()
    {
        return $this->sku && strlen($this->sku)>0;
    }

	/**
	 * @param $obj
	 *
	 * @return self
	 */
    public static function hinted($obj)
    {
    	return $obj;
    }

}