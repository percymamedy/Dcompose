<?php

namespace App\Commands;

use App\Satchel;
use App\Compose;
use App\Laradock;
use App\Support\Artifacts\DockerFolder;
use LaravelZero\Framework\Commands\Command;

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
     * @return mixed
     */
    public function handle()
    {
        // First check if we have Laradock in our
        // satchel and if not we fetch it from
        // GitHub.
        if ($this->satchel->doesNotContainLaradock()) {
            $this->info('Fetching laradock from dist...');
            $this->laradock->grabAndPutInSatchel();
        }

        // Get the Serice the user required.
        $service = $this->argument('service');

        // Checks if Service exists in Laradock.
        if (!$this->laradock->has($service)) {
            $this->error('The service ' . $service . ' is not available in laradock!');
            return;
        }

        // Check if the service exists and if so
        // no need to continue further.
        if ($this->dockerFolder->has($service)) {
            $this->info('The service ' . $service . ' already exists!');
            return;
        }

        // Add the service to the docker-compose.yml.

        // Add the service to the .docker folder.
        $this->dockerFolder->add($service);

        // Persists the .docker folder.
        if ($this->dockerFolder->persist()) {
            $this->info('The service has been added to your docker-compose.yml and to the .docker folder.');
            return;
        }

        $this->error('Unable to save all services in the .docker folder!');

        return;
    }
}
