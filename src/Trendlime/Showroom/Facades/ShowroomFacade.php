<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This file is part of Showroom,
 * a picture management solution for Laravel.
 *
 * @license Apache
 * @package Trendlime\Showroom
 */
class ShowroomFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'showroom';
    }
}