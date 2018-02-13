<?php

namespace JB000\WooCommerce\Models;


/**
 * Class WCLineItemMeta
 * @package JB000\WooooProcessor\Models
 *
 * @property-read int $id
 * @property string $key
 * @property string $value;
 *
 */
class WCMeta extends Model
{

	/**
	 * Getter for the 'value' attribute.
	 *
	 * @return mixed
	 */
	public function getValueAttribute()
	{

		//check if value is serialised
		if($this->isSerialized($this->attributes['value']))
		{
			return unserialize($this->attributes['value']);
		}
		return $this->attributes['value'];
	}

	/**
	 * Taken from the wordpress core.
	 *
	 * //todo move to helper class
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	protected function isSerialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}
		return false;
	}
}
