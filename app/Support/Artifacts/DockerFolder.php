<?php

namespace App\Support\Artifacts;

use App\Laradock;
use App\Support\Paths\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DockerFolder
{
    /**
     * Items we want to save in the Docker folder.
     *
     * @var Collection
     */
    protected $items;

    /**
     * The paths repository instance.
     *
     * @var Repository
     */
    protected $paths;

    /**
     * DockerFolder constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = collect($items);
        $this->paths = resolve(Repository::class);
    }

    /**
     * Load a new instance of the .docker folder.
     *
     * @return DockerFolder
     */
    public static function load(): DockerFolder
    {
        return (new static())->loadServiceFromDisk();
    }

    /**
     * Checks if the Service already exists.
     *
     * @param string $service
     *
     * @return bool
     */
    public function has(string $service): bool
    {
        return $this->items->contains($service);
    }

    /**
     * Add a service to the folder.
     *
     * @param array|string $services
     *
     * @return DockerFolder
     */
    public function add($services): DockerFolder
    {
        $this->items = $this->items->merge((array)$services)
                                   ->unique()
                                   ->values();
        return $this;
    }

    /**
     * Remove a service from the folder.
     *
     * @param array|string $services
     *
     * @return DockerFolder
     */
    public function remove($services): DockerFolder
    {
        $services = (array)$services;

        $this->items = $this->items->reject(function ($item) use ($services) {
            return in_array($item, $services);
        })->values();

        return $this;
    }

    /**
     * Persist all the services.
     *
     * @return bool
     */
    public function persist(): bool
    {
        $allWasPersisted = false;

        foreach ($this->items as $service) {
            $allWasPersisted = $this->persistService($service);
        }

        return $allWasPersisted;
    }

    /**
     * Will tell us if the .docker folder exists.
     *
     * @return bool
     */
    public function folderExists(): bool
    {
        return Storage::disk('work_dir')->exists(
            $this->paths->dockerFolderPath
        );
    }

    /**
     * Will tell us if the .docker folder does not exists.
     *
     * @return bool
     */
    public function folderDoesNotExists(): bool
    {
        return !$this->folderExists();
    }

    /**
     * Creates the .docker folder.
     *
     * @return DockerFolder
     */
    public function touchFolder(): DockerFolder
    {
        Storage::disk('work_dir')->makeDirectory(
            $this->paths->dockerFolderPath
        );

        return $this;
    }

    /**
     * Persist a service.
     *
     * @param $service
     *
     * @return bool
     */
    protected function persistService($service): bool
    {
        if ($this->doesNotHasServiceFolder($service)) {
            recurse_copy(
                resolve(Laradock::class)->servicePath($service) . DIRECTORY_SEPARATOR,
                Storage::disk('docker')->path($service) . DIRECTORY_SEPARATOR
            );
        }

        return $this->hasServiceFolder($service);
    }

    /**
     * Will tell us if the service folder exists.
     *
     * @param string $service
     *
     * @return bool
     */
    protected function hasServiceFolder(string $service): bool
    {
        return Storage::disk('docker')->exists($service);
    }

    /**
     * Will tell us if the service folder does
     * not exists.
     *
     * @param string $service
     *
     * @return bool
     */
    protected function doesNotHasServiceFolder(string $service): bool
    {
        return !$this->hasServiceFolder($service);
    }

    /**
     * Load the services from disk.
     *
     * @return DockerFolder
     */
    protected function loadServiceFromDisk(): DockerFolder
    {
        // Create docker folder if it does not exists.
        if ($this->folderDoesNotExists()) {
            $this->touchFolder();
        }

        $this->items = collect(Storage::disk('docker')->directories());

        return $this;
    }
}
