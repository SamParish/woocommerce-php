<?php


namespace JB000\WooCommerce\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCCoupon
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $code
 * @property double $amount
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property string $discountType
 * @property string $description
 * @property Carbon $dateExpires
 * @property Carbon $dateExpiresGmt
 * @property-read int $usageCount
 * @property bool $individualUse
 * @property array $productIds
 * @property array $excludedProductIds
 * @property int $usageLimit
 * @property int $usageLimitPerUser
 * @property int $limitUsageToXItems
 * @property bool $freeShipping
 * @property array $productCategories
 * @property array $excludedProductCategories
 * @property bool $excludeSaleItems
 * @property double $minimumAmount
 * @property double $maximumAmount
 * @property array $emailRestrictions
 * @property array $usedBy
 * @property Collection $metaData
 */
class WCCoupon extends Model
{
	use CanDelete,
		CanSave;

	protected $endpoint = 'coupons';

	protected $paginate = true;

	protected $date = [
		'date_expires',
		'date_expires_gmt'
	];

	protected $casts = [
		'amount' => 'double',
		'usage_count' => 'int',
		'usage_limit' => 'int',
		'usage_limit_per_user' => 'int',
		'limit_usage_per_x_items' => 'int',
		'free_shipping' => 'bool',
		'exclude_sale_items' => 'bool',
		'minimum_amount' => 'double',
		'maximum_amount' => 'double',
		'meta_data' => [WCMeta::class]
	];

}