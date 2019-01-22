<?php

namespace App\Commands;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
