<?php

namespace JB000\WooCommerce\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Builders\ProductBuilder;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanQuery;
use JB000\WooCommerce\Traits\CanSave;
use JB000\WooCommerce\Traits\HasMeta;

/**
 * Class WCProduct
 *
*@package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property-read string $permalink
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property string $type
 * @property string $status
 * @property bool $featured
 * @property string $catalogVisibility
 * @property string $description
 * @property string $shortDescription
 * @property string $sku
 * @property-read double $price
 * @property double $regularPrice
 * @property double $salePrice
 * @property Carbon $dateOnSaleFrom
 * @property Carbon $dateOnSaleFromGmt
 * @property Carbon $dateOnSaleTo
 * @property Carbon $dateOnSaleToGmt
 * @property-read string $priceHtml
 * @property-read bool $onSale
 * @property-read bool $purchasable
 * @property-read int $totalSales
 * @property bool $virtual
 * @property bool               $downloadable
 * @property Collection         $downloads
 * @property int                $downloadLimit
 * @property int                $downloadExpiry
 * @property string             $externalUrl
 * @property string             $buttonText
 * @property string             $taxStatus
 * @property string             $taxClass
 * @property bool               $manageStock
 * @property int                $stockQuantity
 * @property bool               $inStock
 * @property string             $backorders
 * @property-read bool          $backordersAllowed
 * @property-read bool          $backordered
 * @property bool               $soldIndividually
 * @property string             $weight
 * @property WCProductDimension $dimensions
 * @property-read bool          $shippingRequired
 * @property-read bool          $shippingTaxable
 * @property string             $shippingClass
 * @property-read int           $shippingClassId
 * @property bool               $reviewsAllowed
 * @property-read string        $averageRating
 * @property-read int           $ratingCount
 * @property-read array         $relatedIds
 * @property array              $upsellIds
 * @property array              $crossSellIds
 * @property int                $parentId
 * @property string             $purchaseNote
 * @property Collection         $categories
 * @property Collection         $tags
 * @property Collection $images
 * @property Collection $attributes
 * @property Collection $defaultAttributes
 * @property Collection $variations
 * @property-read array groupedProducts
 * @property int $menuOrder
 * @property Collection $metaData
 */
class WCProduct extends Model
{
    use HasMeta,
        CanSave,
        CanDelete;

    protected $endpoint = 'products';

	protected $paginate = true;

    protected $casts = [
        'featured' => 'bool',
        'price' => 'double',
        'regular_price' => 'double',
        'sale_price' => 'double',
        'on_sale' => 'bool',
        'purchasable' => 'bool',
        'total_sales' => 'int',
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
        'sold_individually' => 'bool',
        'dimensions' => WCProductDimension::class,
        'shipping_required' => 'bool',
        'shipping_taxable' => 'bool',
        'shipping_class_id' => 'int',
        'reviews_allowed' => 'bool',
        'rating_count' => 'int',
        'parent_id' => 'int',
        'categories' => [WCProductCategory::class],
        'tags' => [WCProductTag::class],
        'images' =>  [WCImage::class],
        'attributes' => [WCProductAttribute::class],
        'default_attribute' => [WCProductDefaultAttribute::class],
        'meta_data' => [WCMeta::class],
    ];

    protected $dates = [
        'date_on_sale_from',
        'date_on_sale_from_gmt',
        'date_on_sale_to',
        'date_on_sale_to_gmt',
    ];


    /**
     * @return \JB000\WooCommerce\Builders\Builder
     */
    public function variations()
    {
        return $this->hasChildren(WCProductVariation::class);
    }

    /**
     * Determines if SKU has been set
     *
     * @param null $variationId
     * @return bool
     *
    function isSkuValid($variationId=null)
    {
        $sku = $this->getSku($variationId);
        return ($sku && strlen($sku)>0);
    }

    /**
     * Gets the name of the product
     *
     * @param $variationId
     * @return string
     *
    public function getName($variationId = null)
    {

        $productName = $this->name;

        //check attributes
        $variation = $this->getVariation($variationId);
        if($variation && count($variation->attributes)>0)
        {
            $attributeNames = [];
            foreach($variation->attributes as $attribute)
            {
                $attributeNames[] = $attribute->name.' - '.$attribute->option;
            }
            $productName = implode(', ',$attributeNames);
        }
        return ucwords(strtolower($productName));
    }

    /**
     * Gets the product images
     *
     * @param null $variationId
     * @return WCImage[]
     *
    public function getImages($variationId = null)
    {
        $variation = $this->getVariation($variationId);
        if($variation)
            return $variation->image;

        return $this->images;
    }


    /**
     * Gets the product sku.
     *
     * @param null $variationId
     * @return string|null
     *
    public function getSku($variationId=null)
    {

        $variation = $this->getVariation($variationId);
        $sku = null;
        if($variation && isset($variation->sku))
            $sku = $variation->sku;
        elseif(isset($this->sku))
            $sku = $this->sku;

        return $sku;
    }


    /**
     * Sets the sku on the product, or the product variation if id is supplied
     *
     * @param $sku
     * @param null $variationId
     *
    public function setSku($sku,$variationId=null)
    {
        $variation = $this->getVariation($variationId);
        if($variation)
        {
            //do not set the variation but the object
            foreach($this->variations as $k=>$v)
            {
                if($v->id === $variation->id)
                {
                    $this->variations[$k]->sku = (string)$sku;
                    break;
                }
            }
        }
        else
        {
            $this->sku = (string)$sku;
        }
    }


    /**
     * Gets the product price.
     *
     * @param null $variationId
     * @return string
     *
    public function getPrice($variationId=null)
    {
        $variation = $this->getVariation($variationId);

        $price = ($variation ? $variation->price : $this->price);

        return number_format((float)$price,2);
    }

    /**
     * Determines if stock is managed
     *
     * @param null $variationId
     * @return bool
     *
    public function isStockManagementEnabled($variationId=null)
    {
        $variation = $this->getVariation($variationId);
        if($variation)
            return $variation->manage_stock;

        return $this->manage_stock;
    }


    /**
     * Gets the stock quantity
     *
     * @param null $variationId
     * @return int
     *
    public function getStockQuantity($variationId=null)
    {
        $variation = $this->getVariation($variationId);
        $stockQuantity = ($variation ? $variation->stock_quantity : $this->stock_quantity);
        if($stockQuantity  == null || !is_numeric($stockQuantity ))
            return 0;

        return $stockQuantity ;
    }


    /**
     * Gets the product variations by ID
     *
     * @param $variationId
     * @return WCProductVariation
     *
    public function getVariation($variationId)
    {
        if($variationId == null || !isset($this->variations))
            return null;

        foreach($this->variations as $variation)
        {
            if($variation->id == $variationId)
                return $variation;
        }
        return null;

    }

    /**
     * Determines if product is active
     *
     * @return bool
     *
    public function isActive()
    {
        switch($this->status)
        {
            case WCProductStatus::PUBLISH:
            case WCProductStatus::PRVTE:
                return true;
        }
        return false;
    }
    */

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