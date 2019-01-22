<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class Satchel
{
    /**
     * Satchel disk instance.
     *
     * @var Filesystem
     */
    protected $disk;

    /**
     * Satchel constructor.
     */
    public function __construct()
    {
        $this->disk = Storage::disk('local');
    }
}
