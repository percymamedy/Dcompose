<?php

namespace App\Commands;

use App\Support\Artifacts\DockerFolder;
use App\Support\Artifacts\DockerComposeFile;

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
     * Execute the command process.
     *
     * @return bool
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function fire(): bool
    {
        // Get the Serice the user required.
        $service = $this->argument('service');

        // Remove from Docker folder.
        DockerFolder::load()->remove($service);

        // Remove from docker-compose.yml.
        DockerComposeFile::load()->removeService($service, true)
                                 ->persist();

        $this->info('Service has been removed from .docker foler and docker-compose.yml file!');

        return true;
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
