<?php

namespace App\Commands;

use App\Satchel;
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
     */
    public function __construct(Satchel $satchel, Laradock $laradock)
    {
        parent::__construct();
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

        // Ask User to select services.
        $this->askForServices();

        // Build docker-compose.yml file.

        // Add directories.
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
