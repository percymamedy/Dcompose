<?php

namespace App\Commands;

use App\Support\Artifacts;

class RemoveCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'remove 
                            {service : The service to remove}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a service from the docker-compose.yml and the project';

    /**
     * Success message to show when
     * command is successful.
     *
     * @var string
     */
    protected $successMessage = 'Service has been removed from .docker foler and docker-compose.yml file!';

    /**
     * Execute the command process.
     *
     * @return bool
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function fire(): bool
    {
        $this->removeFromDockerFolder()
             ->removeFromDockerComposeFile();

        return true;
    }

    /**
     * Remove the service from the Docker folder.
     *
     * @return RemoveCommand
     */
    protected function removeFromDockerFolder(): RemoveCommand
    {
        Artifacts\DockerFolder::load()
                              ->remove($this->argument('service'));

        return $this;
    }

    /**
     * Remove the servie from the docker-compose.yml file.
     *
     * @return RemoveCommand
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function removeFromDockerComposeFile(): RemoveCommand
    {
        Artifacts\DockerComposeFile::load()
                                   ->removeService($this->argument('service'), true)
                                   ->persist();

        return $this;
    }

    /**
     * Perform any validation before the command is run.
     *
     * @return Command
     *
     * @throws \InvalidArgumentException|\Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function validate(): Command
    {
        // Get the Serice the user required.
        $service = $this->argument('service');

        // Check if Service exists in .docker folder.
        $this->serviceExistsInDockerFolder($service);

        // Check if Service exist in docker-compose.yml file.
        $this->serviceExistsInDockerComposeFile($service);

        return $this;
    }
}
