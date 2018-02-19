<?php


namespace SamParish\WooCommerce\Models;

use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCAttributeTerm
 *
 * @package SamParish\WooCommerce\Models
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