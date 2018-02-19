<?php


namespace SamParish\WooCommerce\Models;

use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCAttribute
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string $orderBy
 * @property bool $hasArchive
 */
class WCAttribute extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = 'products/attributes';

    protected $casts = [
      'has_archive' => 'bool'
    ];

    /**
     * @return \SamParish\WooCommerce\Builders\Builder
     */
    public function terms()
    {
        return $this->hasChildren(WCAttributeTerm::class);
    }
}