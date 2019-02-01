<?php

namespace App\Commands;

use App\Satchel;
use App\Compose;
use App\Laradock;
use InvalidArgumentException;
use App\Support\Artifacts\DockerFolder;
use LaravelZero\Framework\Commands\Command;
use App\Support\Artifacts\DockerComposeFile;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

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
     * The Compsise instance.
     *
     * @var Compose
     */
    protected $compose;

    /**
     * Satchel instance.
     *
     * @var Satchel
     */
    protected $satchel;

    /**
     * Laradock instance.
     *
     * @var Laradock
     */
    protected $laradock;

    /**
     * DockerFolder instance.
     *
     * @var DockerFolder
     */
    protected $dockerFolder;

    /**
     * RequireCommand constructor.
     *
     * @param Compose  $compose
     * @param Satchel  $satchel
     * @param Laradock $laradock
     */
    public function __construct(Compose $compose, Satchel $satchel, Laradock $laradock)
    {
        parent::__construct();
        $this->compose = $compose;
        $this->dockerFolder = DockerFolder::load();
        $this->satchel = $satchel;
        $this->laradock = $laradock;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            // First check if we have Laradock in our
            // satchel and if not we fetch it from
            // GitHub.
            if ($this->satchel->doesNotContainLaradock()) {
                $this->info('Fetching laradock from dist...');
                $this->laradock->grabAndPutInSatchel();
            }

            // Save service.
            $this->validate()
                 ->saveInDockerFolder()
                 ->saveInDockerComposeFile();

            $this->info('The service has been added to your docker-compose.yml and to the .docker folder.');

            return true;

        } catch (InvalidArgumentException | FileNotFoundException $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    /**
     * Save the service into the .docker folder.
     *
     * @return RequireCommand
     *
     * @throws InvalidArgumentException
     */
    protected function saveInDockerFolder(): RequireCommand
    {
        // Persists the .docker folder and if we are unable to
        // add the service print an error.
        if (!$this->dockerFolder->add($this->argument('service'))->persist()) {
            throw new \InvalidArgumentException('Unable to save all services in the .docker folder!');
        }

        return $this;
    }

    /**
     * Add the service to the docker-compose.yml.
     *
     * @return RequireCommand
     *
     * @throws FileNotFoundException|InvalidArgumentException
     */
    protected function saveInDockerComposeFile(): RequireCommand
    {
        DockerComposeFile::load()
                         ->addService($this->argument('service'), true)
                         ->persist();

        return $this;
    }

    /**
     * Validates if the Service can be added.
     *
     * @return RequireCommand
     *
     * @throws InvalidArgumentException
     */
    protected function validate(): RequireCommand
    {
        // Get the Serice the user required.
        $service = $this->argument('service');

        // Checks if Service exists in Laradock.
        if (!$this->laradock->has($service)) {
            throw new \InvalidArgumentException('The service ' . $service . ' is not available in laradock!');
        }

        // Check if the service exists and if so
        // no need to continue further.
        if ($this->dockerFolder->has($service)) {
            throw new \InvalidArgumentException('The service ' . $service . ' already exists!');
        }

        return $this;
    }
}
