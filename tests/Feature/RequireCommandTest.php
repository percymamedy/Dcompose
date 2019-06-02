<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class RequireCommandTest extends TestCase
{
    /**
     * Test that when init command is run the docker-compose.yml file is
     * created and Dockerfile is created.
     *
     * @return void
     */
    public function test_require_command_add_proper_services_and_files()
    {
        $this->artisan('init')
             ->expectsQuestion('Enter a name for your project', 'foobar')
             ->expectsQuestion('Enter a service name or press enter to quit', 'foo')
             ->expectsQuestion('Enter a service name or press enter to quit', 'bar')
             ->expectsQuestion('Enter a service name or press enter to quit', null)
             ->expectsOutput('Docker environment created run "docker-compose up -d" within the ".docker" directory!')
             ->assertExitCode(0);

        $this->artisan('require', ['service' => 'baz'])
             ->expectsOutput('The service has been added to your docker-compose.yml and to the .docker folder.')
             ->assertExitCode(0);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        Storage::disk('work_dir')->deleteDirectory('.docker');
        parent::tearDown();
    }
}
