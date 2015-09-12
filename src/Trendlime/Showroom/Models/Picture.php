<?php

namespace Trendlime\Showroom\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * This file is part of Showroom,
 * a picture management solution for Laravel.
 *
 * @license Apache
 * @package Trendlime\Showroom
 */
class Picture extends Model
{
    use SoftDeletes;

    public static function notFound()
    {
        return Image::make('img/img-notfound.png');
    }

    /**
     * @param Model $model
     * @param UploadedFile $image
     * @throws FileNotFoundException
     */
    public static function fromUpload(Model $model, UploadedFile $image)
    {
        if ($model === null) {
            throw new ModelNotFoundException;
        }
        if ($image === null) {
            throw new FileNotFoundException;
        }
        if (!$image->isValid()) {
            throw new FileException;
        }

        list($path, $extension) = self::makeImages($model, $image);

        $photo = new Picture();
        $photo->imageable()->associate($model);
        $photo->path = $path;
        $photo->extension = $extension;
        $photo->original_name = $image->getClientOriginalName();
        $photo->save();
    }

    /**
     * @param Model $model
     * @param UploadedFile $image
     * @return array
     */
    private static function makeImages(Model $model, UploadedFile $image)
    {
        $directory = 'uploads/';

        $base = $model->getTable() . '_' . $model->id;

        $extension = '.' . $image->getClientOriginalExtension();

        $path = $directory . $base . '_' . time();

        Image::make($image)
            ->save($path . $extension)
            ->fit(300, 300)
            ->save($path . '_tbn_lrg' . $extension)
            ->fit(75, 75)
            ->save($path . '_tbn_sml' . $extension);

        return [
            $path,
            $extension
        ];
    }

    /**
     * Get the owning imageable model.
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * @param UploadedFile $image
     * @throws FileNotFoundException
     */
    public function replaceImage(UploadedFile $image)
    {
        if ($image === null) {
            throw new FileNotFoundException;
        }
        if (!$image->isValid()) {
            throw new FileException;
        }

        $model = $this->imageable;

        list($path, $extension) = self::makeImages($model, $image);

        File::delete(
            [
                $this->full_path,
                $this->thumbnail_path_large,
                $this->thumbnail_path_small
            ]
        );

        $this->attributes['path'] = $path;
        $this->attributes['extension'] = $extension;
        $this->attributes['original_name'] = $image->getClientOriginalName();
        $this->save();
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $full_path = $this->full_path;
        $thumbnail_large = $this->thumbnail_path_large;
        $thumbnail_small = $this->thumbnail_path_small;

        if (parent::delete()) {
            File::delete(
                [
                    $full_path,
                    $thumbnail_large,
                    $thumbnail_small
                ]
            );

            return true;
        }

        return false;
    }

    public function getFullPathAttribute()
    {
        return $this->path . $this->extension;
    }

    public function getThumbnailPathLargeAttribute()
    {
        return $this->path . '_tbn_lrg' . $this->extension;
    }

    public function getThumbnailPathSmallAttribute()
    {
        return $this->path . '_tbn_sml' . $this->extension;
    }

    public function getImageAttribute()
    {
        return Image::make($this->full_path);
    }

    public function getThumbnailLargeAttribute()
    {
        return Image::make($this->thumbnail_path_large);
    }

    public function getThumbnailSmallAttribute()
    {
        return Image::make($this->thumbnail_path_small);
    }

    // TODO: Write fromURL(Model $model, $url)
}
