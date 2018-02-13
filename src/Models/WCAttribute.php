<?php


namespace JB000\WooCommerce\Models;

use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;

/**
 * Class WCAttribute
 *
 * @package JB000\WooCommerce\Models
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
     * @return \JB000\WooCommerce\Builders\Builder
     */
    public function terms()
    {
        return $this->hasChildren(WCAttributeTerm::class);
    }
}