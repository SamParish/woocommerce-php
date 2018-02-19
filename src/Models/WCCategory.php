<?php


namespace SamParish\WooCommerce\Models;
use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;


/**
 * Class WCProductCategory
 * @package SamParish\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property int $parent
 * @property string $description
 * @property string display
 * @property WCImage $image
 * @property int $menuOrder
 * @property-read int $count
 */
class WCCategory extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'products/categories';

	protected $paginate = true;

    protected $casts = [
        'parent' => 'int',
        'image' => WCImage::class,
        'menu_order' => 'int',
        'count' => 'int'
    ];
}