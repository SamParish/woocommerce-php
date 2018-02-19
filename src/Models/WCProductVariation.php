<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 18/07/2016
 * Time: 12:27
 */

namespace SamParish\WooCommerce\Models;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCProductVariation
 * @package SamParish\WooCommerce\Model
 *
 * @property-read int $id
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property-read string $permalink
 * @property string $sku
 * @property-read double $price
 * @property double $regularPrice
 * @property double $salesPrice
 * @property Carbon $dateOnSaleFrom
 * @property Carbon $dateOnSaleFromGmt
 * @property Carbon $dateOnSaleTo
 * @property Carbon $dateOnSaleToGmt
 * @property-read bool $onSale
 * @property bool $visible
 * @property-read bool $purchasable
 * @property bool $virtual
 * @property bool $downloadable
 * @property Collection $downloads
 * @property int $downloadLimit
 * @property int $downloadExpiry
 * @property string $taxStatus
 * @property string $taxClass
 * @property bool $manageStock
 * @property int $stockQuantity
 * @property bool $inStock
 * @property string $backorders
 * @property-read bool $backordersAllowed
 * @property-read bool $backordered
 * @property string $weight
 * @property WCProductDimension $dimensions
 * @property string $shippingClass
 * @property-read int $shippingClassId
 * @property WCImage $image
 * @property Collection $attributes
 * @property int $menuOrder
 * @property-read Collection $metaData
 */
class WCProductVariation extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'products/{parentId}/variations';

	protected $paginate = true;

    protected $dates = [
        'date_on_sale_from',
        'date_on_sale_from_gmt',
        'date_on_sale_to',
        'date_on_sale_to_gmt'
    ];


    protected $casts = [
        'price' => 'double',
        'regular_price' => 'double',
        'sales_price' => 'double',
        'on_sale' => 'bool',
        'visible' => 'bool',
        'purchasable' => 'bool',
        'virtual' => 'bool',
        'downloadable' => 'bool',
        'downloads' => [WCProductDownload::class],
        'download_limit' => 'int',
        'download_expiry' => 'int',
        'manage_stock' => 'bool',
        'stock_quantity' => 'int',
        'in_stock' => 'bool',
        'backorders_allowed' => 'bool',
        'backordered' => 'bool',
        'dimensions' => WCProductDimension::class,
        'shipping_class_id' => 'int',
        'image' => WCImage::class,
        'attributes' => [WCProductVariationAttribute::class],
        'menu_order' => 'int',
        'meta_data' => [WCMeta::class],
    ];



}