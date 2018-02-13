<?php


namespace JB000\WooCommerce\Models;


use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCTag
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $count
 */
class WCTag extends Model
{

    use CanSave,
        CanDelete;

    protected $endpoint = 'products/tags';

	protected $paginate = true;

    protected $casts = [
        'count' => 'int'
    ];
}