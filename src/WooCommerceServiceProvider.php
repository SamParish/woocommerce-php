<?php
namespace SamParish\WooCommerce;
use Automattic\WooCommerce\Client;
use SamParish\WooCommerce\Models\Model;

class WooCommerceServiceProvider extends \Illuminate\Support\ServiceProvider
{


    public function register()
    {

        //publish config file
        $this->publishes([
            __DIR__.'/config.php'=>config_path('woocommerce.php')
        ]);

        //merge config
        $this->mergeConfigFrom(__DIR__ . '/config.php','woocommerce');


        $this->app->singleton(Client::class,function()
        {
            $client = new Client(
                config('woocommerce.url'),
                config('woocommerce.api_consumer_key'),
                config('woocommerce.api_consumer_secret'),
                config('woocommerce.options')
            );
            return $client;
        });

    }


    public function boot(Client $client)
    {
        Model::setClient($client);
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }

}