<?php

namespace JB000\WooCommerce\Models;
use Carbon\Carbon;

/**
 * Class WCImage
 * @package JB000\WooCommerce\Models
 *
 * @property int $id
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property-read Carbon $dateModified
 * @property-read Carbon $dateModifiedGmt
 * @property string $src
 * @property string $name
 * @property string $alt
 * @property int $position
 */
class WCImage extends Model
{

    protected $casts = [
        'position' => 'int',
    ];

}