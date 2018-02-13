<?php


namespace JB000\WooCommerce\Models;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;


/**
 * Class WCProductCategory
 * @package JB000\WooCommerce\Models
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