<?php


namespace JB000\WooCommerce\Models;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCCustomer
 * @package JB000\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property-read string $role
 * @property string $username
 * @property-write string $password
 * @property WCAddress $billing
 * @property WCAddress $shipping
 * @property-read bool $isPayingCustomer
 * @property-read integer ordersCount
 * @property-read double totalSpent
 * @property string $avatarUrl
 * @property-read Collection $metaData
 */
class WCCustomer extends Model
{

    use CanSave,
        CanDelete;

    protected $endpoint = 'customers';

	protected $paginate = true;

    protected $casts = [
        'billing' => WCAddress::class,
        'shipping' => WCAddress::class,
        'is_paying_customer' => 'bool',
        'orders_count' => 'int',
        'total_spent' => 'double',
        'meta_data' => [WCMeta::class]
    ];
}