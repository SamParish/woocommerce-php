<?php


namespace JB000\WooCommerce\Models;


/**
 * Class WCSetting
 *
 * @package JB000\WooCommerce\Models
 *
 * @property-read string $id
 * @property-read string $label
 * @property-read string $description
 * @property-read string $parentId
 * @property-read string $subGroups
 */
class WCSetting extends Model
{
    protected $endpoint = 'settings';
}