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
        $this->dockerComposePath = 'docker-compose.yml';
        $this->dockerFolderPath = '.docker';
        $this->dockerEnvPath = '.docker' . DIRECTORY_SEPARATOR . '.env';
        $this->dockerEnvExamplePath = '.docker' . DIRECTORY_SEPARATOR . 'env-example';
    }
}
