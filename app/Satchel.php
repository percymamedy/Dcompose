<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * @mixin Filesystem
 */
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

    /**
     * Collect and return laradock folders.
     *
     * @return Collection
     */
    public function laradockData(): Collection
    {
        return collect($this->disk->directories('laradock/data/'))
            ->transform(function ($path) {
                return last(explode(DIRECTORY_SEPARATOR, $path));
            })->reject(function ($item) {
                return in_array($item, ['.github', 'DOCUMENTATION', 'logs']);
            })->values();
    }

    /**
     * Delegates calls to disk.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     *
     * @throws \ErrorException
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->disk, $name)) {
            throw new \ErrorException('Method "' . $name . '" not found on "' . self::class . '" class');
        }

        return $this->disk->{$name}(...$arguments);
    }
}
