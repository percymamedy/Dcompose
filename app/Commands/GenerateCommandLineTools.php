<?php

namespace App\Commands;

use App\Support\Artifacts\Containers;
use LaravelZero\Framework\Commands\Command;
use App\Support\Concerns\CommandResponsable;

class GenerateCommandLineTools extends Command
{
    use CommandResponsable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'generate-tools';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate command line tools for starting and stopping containers.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return Containers::make()->save() ?
            $this->sendSuccessResponse('containers script generated!') :
            $this->sendErrorResponse('Could generate containers script!');
    }
}
