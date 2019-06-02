<?php

namespace App\Support\Artifacts;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class Containers extends AbstractArtifact
{
    /**
     * The path of the stub file.
     *
     * @var string
     */
    protected $stub;

    /**
     * Containers constructor.
     *
     * @param string $stubPath
     */
    public function __construct(string $stubPath = null)
    {
        $this->stub = $stubPath ?? $this->stubDefaultPath();
    }

    /**
     * Saves the artifact and return true
     * if saved was successful.
     *
     * @return bool
     */
    public function save(): bool
    {
        // Copy file to Working directory.
        $wasSaved = Storage::disk('work_dir')->put('containers', $this->stubContents());

        // Make file executed.
        app(Filesystem::class)->chmod(
            Storage::disk('work_dir')->path('containers'), 0755
        );

        return $wasSaved;
    }

    /**
     * Get the command line tools content.
     *
     * @return string
     */
    protected function stubContents(): string
    {
        return file_get_contents($this->stub);
    }

    /**
     * Get the default path where containers stub is
     * stored.
     *
     * @return string
     */
    protected function stubDefaultPath(): string
    {
        return app_path('Commands' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'containers.stub');
    }
}
