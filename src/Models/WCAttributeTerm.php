<?php


namespace JB000\WooCommerce\Models;

use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCAttributeTerm
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $menuOrder
 * @property int $count
 */
class WCAttributeTerm extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'products/attributes/{parentId}/terms';

    protected $casts = [
        'menu_order' => 'int',
        'count' => 'int'
    ];

}