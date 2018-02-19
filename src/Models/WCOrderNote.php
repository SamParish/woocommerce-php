<?php

namespace SamParish\WooCommerce\Models;
use Carbon\Carbon;
use SamParish\WooCommerce\Traits\CanDelete;
use SamParish\WooCommerce\Traits\CanSave;


/**
 * Class WCOrderNote
 * @package SamParish\WooCommerce\Models
 *
 * @property-read int $id;
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property string $note
 * @property bool $customerNote
 */
class WCOrderNote extends Model
{
    use CanSave,
        CanDelete;

    protected $endpoint = "orders/{parentId}/notes";

    protected $casts = [
       'customer_note' => 'bool'
    ];

	protected function performDelete()
	{

		$parent = $this->getRelation('parent');
		return self::$client->delete("orders/{$parent->id}/notes/{$this->id}",['force'=>true]);
	}




}