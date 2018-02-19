<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 14/07/2016
 * Time: 09:36
 */

namespace SamParish\WooCommerce\Models;


/**
 * Class WCAddress
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $company
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $postcode
 * @property string $country
 * @property string $email
 * @property string $phone
 */
class WCAddress extends Model
{

    protected $guarded = [];

    /**
     * Getter for the company attribute
     *
     * @return string|null
     */
    public function getCompanyAttribute()
    {
        //strip invalid company names. These are commonly entered company names that are incorrect
        $incorrectSettings = ['na','n/a','-','none','ms.','ms','mr.','mr','mrs.','mrs','private','personal use','personal','home'];

        if(in_array(strtolower($this->attributes['company']),$incorrectSettings))
            return null;

        return $this->attributes['company'];

    }

	/**
	 * Determines if address is empty
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		$empty = false;
		foreach($this->attributes as $attribute)
		{
			if($attribute)
			{
				$empty = true;
				break;
			}
		}

		return !$empty;

	}


}