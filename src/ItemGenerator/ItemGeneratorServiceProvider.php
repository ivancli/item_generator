<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/26/2017
 * Time: 4:05 PM
 */
namespace IvanCLI\ItemGenerator;

use Illuminate\Support\ServiceProvider;

class ItemGeneratorServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->registerItemGenerator();
    }

    private function registerItemGenerator()
    {
        $this->app->bind('item_generator', function ($app) {
            return new ItemGenerator($app);
        });
    }
}