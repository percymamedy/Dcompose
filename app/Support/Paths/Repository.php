<?php

namespace App\Support\Paths;

class Repository
{
    /**
     * File path of docker-compose.yml file.
     *
     * @var string
     */
    public $dockerComposePath;

    /**
     * Path to the .docker folder.
     *
     * @var string
     */
    public $dockerFolderPath;

    /**
     * Path to the .docker/.env file.
     *
     * @var string
     */
    public $dockerEnvPath;

    /**
     * Path to the .docker/env-example file.
     *
     * @var string
     */
    public $dockerEnvExamplePath;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->dockerFolderPath = '.docker';
        $this->dockerComposePath = $this->dockerFolderPath . DIRECTORY_SEPARATOR . 'docker-compose.yml';
        $this->dockerEnvPath = $this->dockerFolderPath . DIRECTORY_SEPARATOR . '.env';
        $this->dockerEnvExamplePath = $this->dockerFolderPath . DIRECTORY_SEPARATOR . 'env-example';
    }
}
