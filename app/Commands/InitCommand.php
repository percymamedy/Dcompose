<?php

namespace App\Commands;

class InitCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialises docker-compose.yml and Dockerfile inside their respective directories';

    /**
     * Choosen services for initialization.
     *
     * @var array
     */
    protected $choosenServices = [];

    /**
     * Success message to show when
     * command is successful.
     *
     * @var string
     */
    protected $successMessage = 'Docker environment created run "docker-compose up -d" within the ".docker" directory!';

    /**
     * Project name.
     *
     * @var string
     */
    protected $projectName;

    /**
     * Execute the command process.
     *
     * @return bool
     */
    protected function fire(): bool
    {
        $this->askForProjectName()
             ->askForServices()
             ->createDockerComposeFile()
             ->createDockerFolder()
             ->addServicesToThem();

        return true;
    }

    /**
     * Ask User for a Project name.
     *
     * @return InitCommand
     */
    protected function askForProjectName(): InitCommand
    {
        $this->projectName = $this->ask('Enter a name for your project');

        return $this;
    }

    /**
     * Ask User for services.
     *
     * @return InitCommand
     */
    protected function askForServices(): InitCommand
    {
        // Get available services.
        $availableServices = $this->laradock->availableServices();

        while (true) {
            // Ask User for services.
            $service = $this->askWithCompletion('Enter a service name or press enter to quit', $availableServices->all());

            // User completed his selection of services.
            if (is_null($service)) {
                break;
            }

            // Validate Service.
            if ($availableServices->contains($service)) {
                $this->choosenServices[] = $service;
                continue;
            }

            // Show error.
            $this->error('The service "' . $service . '" is not a valid laradock service. Enter another service');
        }

        return $this;
    }

    /**
     * Build docker-compose.yml file.
     *
     * @return InitCommand
     *
     * @throws \InvalidArgumentException
     */
    protected function createDockerComposeFile(): InitCommand
    {
        if (!$this->compose->newUpDockerComposeFile($this->choosenServices)) {
            throw new \InvalidArgumentException('Unable to create docker-compose.yml file!');
        }

        return $this;
    }

    /**
     * Add choosen services to docker-compose.yml file and
     * to the .docker folder.
     *
     * @return InitCommand
     */
    protected function addServicesToThem(): InitCommand
    {
        $this->compose->addServices($this->choosenServices);

        return $this;
    }

    /**
     * Create the .docker folder.
     *
     * @return InitCommand
     */
    protected function createDockerFolder(): InitCommand
    {
        $this->compose->touchDockerFolder($this->projectName);

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
        // Cancel action if the User does not want to continue
        // with erasing the docker-compose.yml file.
        if ($this->compose->hasDockerComposeFile() && !$this->confirm('The docker-compose.yml file exist, do you wish to continue?')) {
            throw new \InvalidArgumentException('Operation aborted!');
        }

        return $this;
    }
}
