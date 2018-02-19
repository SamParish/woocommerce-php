<?php


namespace SamParish\WooCommerce\Models;


/**
 * Class WCSetting
 *
 * @package SamParish\WooCommerce\Models
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