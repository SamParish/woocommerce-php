<?php


namespace JB000\WooCommerce\Models;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JB000\WooCommerce\Traits\CanDelete;
use JB000\WooCommerce\Traits\CanSave;
use JB000\WooCommerce\Traits\HasMeta;


/**
 * Class WCShippingLineItem
 * @package JB000\WooooProcessor\Models
 *
 * @property-read integer $id
 * @property int $orderId
 * @property-read Carbon $dateCreated
 * @property-read Carbon $dateCreatedGmt
 * @property string $amount
 * @property string $reason
 * @property int $refundedBy
 * @property-read Collection $metaData
 * @property-read Collection $lineItems
 * @property-write bool $apiRefund
 */
class WCRefund extends Model
{
	use HasMeta
	{
		getMeta as refundGetMeta;
		storeMeta as refundStoreMeta;
	}
	use CanSave;
	use CanDelete;

	protected $guarded = [];

	protected $paginate = true;

	protected $endpoint = 'orders/{parentId}/refunds';

	protected $casts  = [
		'refunded_by' => 'int',
		'meta_data' => [WCMeta::class],
		'line_items' => [WCLineItem::class],
		'api_refund' => 'bool'
	];


	/**
	 * Gets meta for the refund.
	 *
	 * We have to take into account the refunds cannot be updated via the API.
	 *
	 * A workaround is to store the meta on the order for refunds that have been saved.
	 *
	 * @param      $key
	 * @param bool $single
	 * @return null
	 */
	public function getMeta($key, $single=true)
	{
		//first check meta on the refund object itself for backwards compatibility
		if($value = $this->refundGetMeta($key,$single))
			return $value;

		//we can only check the order if the refund is saved
		$order=$this->getRelation('parent');
		if($this->exists && $order)
			return $order->getMeta("_refund_{$this->id}_$key",$single);

		return null;

	}

	/**
	 * Stores meta for the refund.
	 *
	 * We have to take into account that refunds cannot be updated via the API.
	 *
	 * A workaround is to store the meta on the order for refunds that have saved.
	 *
	 * @param      $key
	 * @param      $value
	 * @param bool $single
	 * @throws \Exception
	 */
	public function storeMeta($key, $value,$single=true)
	{
		//if its not been saved, then we can store it on the refund object itself.
		$order = $this->getRelation('parent');
		if(!$this->exists)
		{
			$this->refundStoreMeta($key, $value, $single);
		}
		else if($order)
		{
			$order->storeMeta("_refund_{$this->id}_$key",$value,$single);
		}
		else
		{
			throw new \Exception("Unable to save meta until refund as been saved");
		}
	}

	protected function beforeSave()
	{
		//save the order as storing meta on the order requires it to be saved
		$order = $this->getRelation('parent');
		$order->save();
	}

	protected function performInsert($attributes = [])
	{
		$order = $this->getRelation('parent');
		return self::$client->post("orders/{$order->id}/refunds",$attributes);
	}

	protected function performUpdate($attributes = [])
	{
		throw new \Exception("Refund cannot be updated");
	}

	protected function performDelete()
	{
		$order = $this->getRelation('parent');
		return self::$client->delete("orders/{$order->id}/refunds/{$this->id}");
	}
}