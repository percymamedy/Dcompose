<?php

namespace App\Commands;

class RefreshCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'refresh';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Refetches laradock from dist';

    /**
     * Success message to show when
     * command is successful.
     *
     * @var string
     */
    protected $successMessage = 'laradock has been refreshed';

    /**
     * Error message to show when
     * command fails.
     *
     * @var string
     */
    protected $errorMessage = 'Error fetching laradock from dist';

    /**
     * Execute the command process.
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function fire(): bool
    {
        return true;
    }

    /**
     * Check if we should refetch laradock or not.
     *
     * @return bool
     */
    protected function shouldFetchLaradock(): bool
    {
        return true;
    }

    /**
     * Perform any validation before the command is run.
     *
     * @return Command
     *
     * @throws \InvalidArgumentException
     */
    protected function validate(): Command
    {
        return $this;
    }
}
