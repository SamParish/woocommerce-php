<?php


namespace SamParish\WooCommerce\Models;


use Illuminate\Database\Eloquent\Collection;
use SamParish\WooCommerce\Traits\CanSave;

/**
 * Class WCPaymentGateway
 *
 * @package SamParish\WooCommerce\Models
 *
 * @property-read string $id
 * @property string $title
 * @property string $description
 * @property int $order
 * @property bool $enabled
 * @property-read string $methodTitle
 * @property-read string $methodDescription
 * @property Collection $settings
 */
class WCPaymentGateway extends Model
{

    use CanSave;

    protected $endpoint = 'payment_gateways';

    protected $casts = [
        'order' => 'int',
        'enabled' => 'bool',
        'settings' => [WCPaymentGatewaySetting::class]
    ];


    protected function beforeSave()
    {
        //can only update
        if(!$this->exists)
            throw new \Exception("Cannot create new payment gateways through API");
    }

}