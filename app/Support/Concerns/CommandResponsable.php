<?php

namespace App\Support\Concerns;

trait CommandResponsable
{
    /**
     * Show a success message and the exit the program.
     *
     * @param  string  $message
     *
     * @return void
     */
    protected function sendSuccessResponse(string $message = null): void
    {
        $message = $message ?? 'Command successfully ran!';

        $this->info($message);
    }

    /**
     * Show an error message and then exits the program.
     *
     * @param  string  $message
     *
     * @return void
     */
    protected function sendErrorResponse(string $message = null): void
    {
        $message = $message ?? 'Command failed !';

        $this->error($message);
    }
}
