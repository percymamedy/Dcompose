<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class InitCommandTest extends TestCase
{
    /**
     * Test that when init command is run the docker-compose.yml file is
     * created and Dockerfile is created.
     *
     * @return void
     */
    public function test_init_command_creates_docker_compose_and_docker_files()
    {
        $this->artisan('init')
             ->expectsQuestion('Enter a name for your project', 'foobar')
             ->expectsQuestion('Enter a service name or press enter to quit', 'foo')
             ->expectsQuestion('Enter a service name or press enter to quit', 'bar')
             ->expectsQuestion('Enter a service name or press enter to quit', null)
             ->expectsOutput('Docker environment created run "docker-compose up -d" within the ".docker" directory!')
             ->assertExitCode(0);

        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/docker-compose.yml');
        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/.env');
        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/env-example');
        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/foo/Dockerfile');
        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/bar/Dockerfile');
        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/.docker/bar/assets.txt');
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        Storage::disk('work_dir')->deleteDirectory('.docker');
        parent::tearDown();
    }
}
