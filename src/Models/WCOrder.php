<?php

namespace SamParish\WooCommerce\Models;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use SamParish\WooCommerce\Builders\Builder;
use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;
use SamParish\WooCommerce\Traits\HasMeta;

/**
 * Class WCOrder
 * @package SamParish\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property integer $parentId
 * @property-read string $number
 * @property-read string $orderKey
 * @property-read string $createdVia
 * @property-read string $version
 * @property string $status
 * @property string $currency
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property-read double $discountTotal
 * @property-read double $discountTax
 * @property-read double $shippingTotal
 * @property-read double $shippingTax
 * @property-read double $cartTax
 * @property-read double $total
 * @property-read double $totalTax
 * @property-read bool $pricesIncludeTax
 * @property integer $customerId
 * @property-read string $customerIpAddress
 * @property-read string $customerUserAgent
 * @property string $customerNote
 * @property WCAddress $billing
 * @property WCAddress $shipping
 * @property string $paymentMethod
 * @property string $paymentMethodTitle
 * @property string $transactionId
 * @property-read Carbon|null $datePaid
 * @property-read Carbon|null $datePaidGmt
 * @property-read Carbon|null $dateCompleted
 * @property-read Carbon|null $dateCompletedGmt
 * @property-read string $cartHash
 * @property-read Collection $metaData
 * @property-read Collection $lineItems
 * @property-read Collection $taxLines
 * @property-read Collection $shippingLines
 * @property-read Collection $feeLines
 * @property-read Collection $couponLines
 * @property-read Collection $refunds
 * @property-read Collection $notes
 * @property-write bool $setPaid
 * @property-read string $email
 */
class WCOrder extends Model
{
    use HasMeta,
        CanSave,
	    CanDelete;

    protected $endpoint = 'orders';

	protected $paginate = true;




	protected $casts = [
		'parent_id' => 'int',
        'discount_total' => 'double',
        'discount_tax' => 'double',
        'shipping_total' => 'double',
        'shipping_tax' => 'double',
        'cart_tax' => 'double',
        'total' => 'double',
        'total_tax' => 'double',
		'price_include_tax' => 'bool',
		'customer_id' => 'int',
        'billing' => WCAddress::class,
        'shipping' => WCAddress::class,
        'line_items' => [WCLineItem::class],
        'tax_lines' => [WCTaxLineItem::class],
        'shipping_lines' => [WCShippingLineItem::class],
        'fee_lines' => [WCFeeLineItem::class],
        'coupon_lines' => [WCCouponLineItem::class],
        'meta_data' => [WCMeta::class],
	];


	protected $dates = [
		'date_created_gmt',
		'date_modified_gmt',
		'date_completed',
		'date_completed_gmt',
		'date_paid',
		'date_paid_gmt'
	];

    /**
     * @return Builder
     */
    public function refunds()
    {
        return $this->hasChildren(WCRefund::class);
    }

    /**
     * @return Builder
     */
    public function notes()
    {
        return $this->hasChildren(WCOrderNote::class);
    }



    /**
     * Getter for the 'displayName' attribute.
     *
     * Returns the Company Name, If the company name is not present then returns individual name
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        //check to see if we have a company name
        if($company = $this->billing->company)
            return $company;


        $name = $this->billing->firstName;

        //if we have a last name, then also include that
        if($this->billing->lastName)
            $name .= ' '.$this->billing->lastName;

        return $name;
    }



    /**
     * Getter for the 'email' attribute
     *
     * @return null|string
     */
    public function getEmailAttribute()
    {
        return strtolower(trim($this->billing->email));
    }


	/**
	 * Setter for the 'currency' attribute
	 *
	 * @param $value
	 */
	public function setCurrencyAttribute($value)
	{
		$this->attributes['currency'] = strtoupper($value);
	}



	/**
     * @param $obj
     * @return self
     */
    public static function hinted($obj)
    {
        return $obj;
    }
}