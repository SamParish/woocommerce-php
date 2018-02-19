<?php


namespace SamParish\WooCommerce\Models;


use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCTag
 *
 * @package SamParish\WooCommerce\Models
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