<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class GenerateCommandTest extends TestCase
{
    /**
     * Test that the generate-tools command work
     * as expected
     *
     * @return void
     */
    public function test_when_generate_tools_command_creates_the_containers_script()
    {
        $this->artisan('generate-tools')
             ->expectsOutput('containers script generated!')
             ->assertExitCode(0);

        $this->assertFileExists(__DIR__ . '/../fixtures/work_dir/containers');
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        Storage::disk('work_dir')->delete('containers');
        parent::tearDown();
    }
}
