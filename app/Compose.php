<?php

namespace App;

use App\Support\Paths\Repository;
use App\Support\Artifacts\DockerComposeFile;
use Illuminate\Contracts\Filesystem\Filesystem;

class Compose
{
    /**
     * Storage instance.
     *
     * @var Filesystem
     */
    protected $storage;

    /**
     * Laradock instance.
     *
     * @var Laradock
     */
    protected $laradock;

    /**
     * Paths repository instance.
     *
     * @var Repository
     */
    protected $paths;

    /**
     * Compose constructor.
     *
     * @param Filesystem $storage
     * @param Laradock   $laradock
     * @param Repository $paths
     */
    public function __construct(Filesystem $storage, Laradock $laradock, Repository $paths)
    {
        $this->storage = $storage;
        $this->laradock = $laradock;
        $this->paths = $paths;
    }

    /**
     * Checks if the docker-compose.yml file exists already.
     *
     * @return bool
     */
    public function hasDockerComposeFile(): bool
    {
        return $this->storage->exists($this->paths->dockerComposePath);
    }

    /**
     * Checks if the .docker folder exists.
     *
     * @return bool
     */
    public function hasDockerFolder(): bool
    {
        return $this->storage->exists($this->paths->dockerFolderPath);
    }

    /**
     * Checks if the .docker/.env file exists.
     *
     * @return bool
     */
    public function hasDockerEnv(): bool
    {
        return $this->storage->exists($this->paths->dockerEnvPath);
    }

    /**
     * New up the docker-compose.yml file.
     *
     * @param array $services
     *
     * @return bool
     */
    public function newUpDockerComposeFile(array $services): bool
    {
        try {
            // Erase previous docker-compse.yml file.
            $this->storage->delete($this->paths->dockerComposePath);

            // New up a DockerCompose file artifact with
            // the Contents of the original
            // docker-compose file from
            // laradock.
            $dockerComposeFile = DockerComposeFile::make(
                $this->laradock->dockerComposeData(), $services
            );

            // Add commons to all docker-compose.yml.
            $contents = $dockerComposeFile->addCommons()
                                          ->addServices()
                                          ->render();

            // Save docker-compose.yml file.
            return $this->storage->put($this->paths->dockerComposePath, $contents);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Creates .docker folder and add in the .env file.
     *
     * @return bool
     */
    public function touchDockerFolder(): bool
    {
        try {
            // Checks if .docker folder exists
            // and if not create it.
            if (!$this->hasDockerFolder()) {
                $this->storage->makeDirectory(
                    $this->paths->dockerFolderPath
                );
            }

            // Checks if .env file exists and
            // if not create it.
            if (!$this->hasDockerEnv()) {
                // Get .env data.
                $envData = $this->laradock->envData();
                // Create env-example file.
                $this->storage->put($this->paths->dockerEnvExamplePath, $envData);
                // Create .env file.
                $this->storage->put($this->paths->dockerEnvPath, $envData);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add Services to the .docker/ folder.
     *
     * @param string|array $services
     *
     * @return bool
     */
    public function addServices($services): bool
    {
        $services = (array)$services;

        foreach ($services as $service) {
            // Copy Service folder to the .docker folder.
            recurse_copy(
                $this->laradock->servicePath($service) . DIRECTORY_SEPARATOR,
                $this->storage->path($this->paths->dockerFolderPath . DIRECTORY_SEPARATOR . $service . DIRECTORY_SEPARATOR)
            );
        }

        return true;
    }
}
