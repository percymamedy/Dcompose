<?php

namespace App\Commands;

use App\Compose;
use App\Satchel;
use App\Laradock;
use Illuminate\Support\Facades\Storage;
use App\Support\Artifacts\DockerComposeFile;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LaravelZero\Framework\Commands\Command as BaseCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

abstract class Command extends BaseCommand
{
    /**
     * Laradock instance.
     *
     * @var Laradock
     */
    protected $laradock;

    /**
     * Satchel instance.
     *
     * @var Satchel
     */
    protected $satchel;

    /**
     * The Compose instance.
     *
     * @var Compose
     */
    protected $compose;

    /**
     * Success message to show when
     * command is successful.
     *
     * @var string
     */
    protected $successMessage;

    /**
     * Error message to show when
     * command fails.
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Command constructor.
     *
     * @param Laradock $laradock
     * @param Satchel  $satchel
     * @param Compose  $compose
     */
    public function __construct(Laradock $laradock, Satchel $satchel, Compose $compose)
    {
        parent::__construct();

        $this->laradock = $laradock;
        $this->satchel = $satchel;
        $this->compose = $compose;
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        try {
            $this->fetchLaradockIfNecessary()
                 ->validate();
        } catch (\Exception $e) {
            $this->sendFailedResponse($e);
        }
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            // Run the Command and if it's success full
            // call the success method.
            if ($this->fire()) {
                return $this->sendSuccessResponse();
            }

            return $this->sendErrorResponse();
        } catch (\Exception $e) {
            $this->sendFailedResponse($e);
            return false;
        }
    }

    /**
     * Show a sucess message and the exit the
     * program.
     *
     * @return bool
     */
    protected function sendSuccessResponse(): bool
    {
        $message = $this->successMessage ?? 'Command successfully ran!';

        $this->info($message);

        return true;
    }

    /**
     * Show an error message and then exits the
     * program.
     *
     * @return bool
     */
    protected function sendErrorResponse(): bool
    {
        $message = $this->errorMessage ?? 'Command failed !';

        $this->error($message);

        return false;
    }

    /**
     * Perform any validation before the command is run.
     *
     * @return Command
     *
     * @throws \InvalidArgumentException
     */
    protected abstract function validate(): Command;

    /**
     * Execute the command process.
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected abstract function fire(): bool;

    /**
     * Fetch Laradock and put it inside the Satchel if
     * it is not present.
     *
     * @return Command
     */
    protected function fetchLaradockIfNecessary(): Command
    {
        // First check if we have Laradock in our
        // satchel and if not we fetch it from
        // GitHub.
        if ($this->satchel->doesNotContainLaradock()) {
            $this->info('Fetching laradock from dist...');
            $this->laradock->grabAndPutInSatchel();
        }

        return $this;
    }

    /**
     * Validate that the service is available in Laradock folder.
     *
     * @param string $service
     *
     * @throws \InvalidArgumentException
     */
    protected function serviceIsAvailableInLaradock(string $service)
    {
        // Checks if Service exists in Laradock.
        if (!$this->laradock->has($service)) {
            throw new \InvalidArgumentException('The service "' . $service . '" is not available in laradock!');
        }
    }

    /**
     * Validate that the service is available in Docker folder.
     *
     * @param string $service
     *
     * @throws \InvalidArgumentException
     */
    protected function serviceExistsInDockerFolder(string $service)
    {
        // Checks if Service exists in .docker folder.
        if (!Storage::disk('docker')->exists($service)) {
            throw new \InvalidArgumentException('The service "' . $service . '" is not available in your .docker folder!');
        }
    }

    /**
     * Validate that the service is not available in Docker folder.
     *
     * @param string $service
     *
     * @throws \InvalidArgumentException
     */
    protected function serviceShouldNotExistInDockerFolder(string $service)
    {
        // Checks if Service exists in .docker folder.
        if (Storage::disk('docker')->exists($service)) {
            throw new \InvalidArgumentException('The service "' . $service . '" already exists in your .docker folder!');
        }
    }

    /**
     * Validate that the service is available in docker-compose.yml file.
     *
     * @param string $service
     *
     * @throws \InvalidArgumentException|FileNotFoundException
     */
    protected function serviceExistsInDockerComposeFile(string $service)
    {
        // Checks if Service exists in .docker folder.
        if (!DockerComposeFile::load()->exists($service)) {
            throw new \InvalidArgumentException('The service "' . $service . '" is not available in your .docker folder!');
        }
    }

    /**
     * Output an error message.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function sendFailedResponse(\Exception $e)
    {
        $this->error($e->getMessage());
        exit(1);
    }
}
