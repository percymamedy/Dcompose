<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
