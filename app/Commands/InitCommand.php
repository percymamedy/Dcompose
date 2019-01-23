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
     * InitCommand constructor.
     *
     * @param Satchel $satchel
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
    }
}
