<?php

namespace Trendlime\Showroom\Reusables;

use Trendlime\Showroom\Models\Picture;

/**
 * This file is part of Showroom,
 * a picture management solution for Laravel.
 *
 * @license Apache
 * @package Trendlime\Showroom
 */
trait PictureableTrait
{
    /**
     * Get all of the user's photos.
     */
    public function photos()
    {
        return $this->morphMany(Picture::class, 'imageable');
    }
}