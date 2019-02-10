<?php

namespace App\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class GenerateCommandLineTools extends Command
{
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
        // Copy file to Working directory.
        Storage::disk('work_dir')->put('containers', $this->getContainersFileContents());

        // Make file executed.
        app(Filesystem::class)->chmod(
            Storage::disk('work_dir')->path('containers'), 0755
        );

        $this->info('containers script generated!');

        return true;
    }

    /**
     * Get the command line tools content.
     *
     * @return string
     */
    protected function getContainersFileContents(): string
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'containers.stub');
    }
}
