<?php


namespace JB000\WooCommerce\Traits;


use Illuminate\Support\Collection;
use JB000\WooCommerce\Models\WCMeta;

trait HasMeta
{

	/**
	 * @param string $key
	 * @param bool $single
	 * @return Collection|null|string
	 */
	public function getMeta($key,$single = true)
	{
		$meta = $this->metaData->where('key',$key);

		if($meta->count() == 0)
			return null;

		if($single)
			return $meta->first()->value;

		return $meta;
	}



	/**
     * @param      $key
     * @param      $value
     * @param bool $single
     */
    public function storeMeta($key,$value,$single = true)
    {
        $meta = null;
        if($single)
            $meta = $this->metaData->where('key',$key)->first();

        if($meta == null)
        {
            $meta = new WCMeta();
            $meta->key = $key;
            $this->metaData->push($meta);
        }
        $meta->value = $value;
    }
}
