<?php

namespace App\Commands;

use App\Satchel;
use App\Compose;
use App\Laradock;
use LaravelZero\Framework\Commands\Command;

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
     * The Compose instance.
     *
     * @var Compose
     */
    protected $compose;

    /**
     * Choosen services for initialization.
     *
     * @var array
     */
    protected $choosenServices = [];

    /**
     * InitCommand constructor.
     *
     * @param Satchel  $satchel
     * @param Laradock $laradock
     * @param Compose  $compose
     */
    public function __construct(Satchel $satchel, Laradock $laradock, Compose $compose)
    {
        parent::__construct();
        $this->satchel = $satchel;
        $this->laradock = $laradock;
        $this->compose = $compose;
    }

    /**
     * Execute the console command.
     *
     * @return mixed|void
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

        // Ask User for a Project name.
        $projectName = $this->ask('Enter a name for your project');

        // Ask User to select services.
        $this->askForServices();

        // Cancel action if the User does not want to continue with erasing the docker-compose.yml file.
        if ($this->compose->hasDockerComposeFile() && !$this->confirm('The docker-compose.yml file exist, do you wish to continue?')) {
            $this->info('Operation aborted!');
            return;
        }

        // Build docker-compose.yml file.
        if (!$this->compose->newUpDockerComposeFile($this->choosenServices)) {
            $this->error('Unable to create docker-compose.yml file');
            return;
        }

        // Create .docker folder.
        $this->compose->touchDockerFolder($projectName);

        // Add directories.
        $this->compose->addServices($this->choosenServices);

        // Success message.
        $this->info('Docker environment created run "docker-compose up -d" within the ".docker" directory');
    }

    /**
     * Ask User for services.
     *
     * @return void
     */
    protected function askForServices()
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
    }
}
