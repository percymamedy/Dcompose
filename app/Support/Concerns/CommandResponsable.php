<?php

namespace App\Support\Concerns;

trait CommandResponsable
{
    /**
     * Show a sucess message and the exit the program.
     *
     * @param string $message
     *
     * @return bool
     */
    protected function sendSuccessResponse(string $message = null): bool
    {
        $message = $message ?? 'Command successfully ran!';

        $this->info($message);

        return true;
    }

    /**
     * Show an error message and then exits the program.
     *
     * @param string $message
     *
     * @return bool
     */
    protected function sendErrorResponse(string $message = null): bool
    {
        $message = $message ?? 'Command failed !';

        $this->error($message);

        return false;
    }
}
