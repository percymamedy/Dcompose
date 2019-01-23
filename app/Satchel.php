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

    /**
     * Checks Satchel contains Laradock.
     *
     * @return bool
     */
    public function containsLaradock(): bool
    {
        return $this->disk->exists('laradock.zip');
    }

    /**
     * Checks Satchel does not contains Laradock.
     *
     * @return bool
     */
    public function doesNotContainLaradock(): bool
    {
        return !$this->containsLaradock();
    }
}
