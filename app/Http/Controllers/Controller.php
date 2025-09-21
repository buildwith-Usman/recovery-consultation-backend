<?php

namespace App\Http\Controllers;
use Illuminate\Http\UploadedFile;

abstract class Controller
{
    /**
     * Upload file
     */
    public function uploadImage(UploadedFile $image, string $directory = 'uploads'): string
    {
        return $image->store($directory, 'public');
    }
}
