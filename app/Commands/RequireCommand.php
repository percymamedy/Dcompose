<?php

namespace App\Commands;

use App\Support\Artifacts;

class RequireCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'require 
                            {service : The service to add to the project}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a service to the docker-compose.yml and to the project';

    /**
     * Success message to show when
     * command is successful.
     *
     * @var string
     */
    protected $successMessage = 'The service has been added to your docker-compose.yml and to the .docker folder.';

    /**
     * Execute the command process.
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function fire(): bool
    {
        $this->saveInDockerFolder()
             ->saveInDockerComposeFile();

        return true;
    }


    /**
     * Save the service into the .docker folder.
     *
     * @return RequireCommand
     *
     * @throws \InvalidArgumentException
     */
    protected function saveInDockerFolder(): RequireCommand
    {
        Artifacts\DockerFolder::load()
                              ->add($this->argument('service'))
                              ->persist();

        return $this;
    }

    /**
     * Add the service to the docker-compose.yml.
     *
     * @return RequireCommand
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     */
    protected function saveInDockerComposeFile(): RequireCommand
    {
        Artifacts\DockerComposeFile::load()
                                   ->addService($this->argument('service'), true)
                                   ->persist();

        return $this;
    }

    /**
     * Perform any validation before the command is run.
     *
     * @return Command
     *
     * @throws \InvalidArgumentException
     */
    protected function validate(): Command
    {
        // Get the Serice the user required.
        $service = $this->argument('service');

        // Service should exist in Laradock.
        $this->serviceIsAvailableInLaradock($service);

        // Service should not exist in Docker folder.
        $this->serviceShouldNotExistInDockerFolder($service);

        return $this;
    }
}
