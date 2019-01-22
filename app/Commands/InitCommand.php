<?php

namespace App\Commands;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
