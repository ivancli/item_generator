<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/26/2017
 * Time: 4:04 PM
 */
namespace IvanCLI\ItemGenerator;

use Illuminate\Support\Facades\Facade;

class ItemGeneratorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'item_generator';
    }
}