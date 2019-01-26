<?php

namespace App;

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
     * File path of docker-compose.yml file.
     *
     * @var string
     */
    protected $dockerComposePath;

    /**
     * Compose constructor.
     *
     * @param Filesystem $storage
     * @param Laradock   $laradock
     * @param string     $dockerComposePath
     */
    public function __construct(Filesystem $storage, Laradock $laradock, string $dockerComposePath)
    {
        $this->storage = $storage;
        $this->laradock = $laradock;
        $this->dockerComposePath = $dockerComposePath;
    }

    /**
     * Checks if the docker-compose.yml file exists already.
     *
     * @return bool
     */
    public function hasDockerComposeFile(): bool
    {
        return $this->storage->exists($this->dockerComposePath);
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
            $this->storage->delete($this->dockerComposePath);

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
            return $this->storage->put($this->dockerComposePath, $contents);
        } catch (\Exception $e) {
            return false;
        }
    }


    public function addServices($services)
    {
    }
}
