<?php

namespace App\Support\Artifacts;

use  Illuminate\Support\Str;
use App\Support\Paths\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;

class EnvFile
{
    /**
     * Original env data.
     *
     * @var string
     */
    protected $originalData;

    /**
     * Final env data.
     *
     * @var string
     */
    protected $data;

    /**
     * EnvFile constructor.
     *
     * @param string $envData
     */
    public function __construct(string $envData)
    {
        $this->originalData = $envData;
        $this->data = $envData;
    }

    /**
     * Set Project name to the env data.
     *
     * @param string $projectName
     *
     * @return EnvFile
     */
    public function setProjectName(string $projectName): EnvFile
    {
        return $this->updateEnvData('COMPOSE_PROJECT_NAME', $projectName)
                    ->updateEnvData('DATA_PATH_HOST', '~/.laradock/' . Str::snake($projectName . '_data'));
    }

    /**
     * Save env-example.
     *
     * @param Filesystem $disk
     *
     * @return bool
     */
    public function saveExample(Filesystem $disk): bool
    {
        return $this->saveAt($disk, $this->paths()->dockerEnvExamplePath);
    }

    /**
     * Save env-example.
     *
     * @param Filesystem $disk
     *
     * @return bool
     */
    public function save(Filesystem $disk): bool
    {
        return $this->saveAt($disk, $this->paths()->dockerEnvPath);
    }

    /**
     * Save env data at the given path on the given disk.
     *
     * @param Filesystem $disk
     * @param string     $path
     *
     * @return bool
     */
    public function saveAt(Filesystem $disk, string $path): bool
    {
        return $disk->put($path, $this->render());
    }

    /**
     * Render the env data.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->data;
    }

    /**
     * Update a data in the env data.
     *
     * @param string $key
     * @param string $value
     *
     * @return EnvFile
     */
    public function updateEnvData(string $key, string $value): EnvFile
    {
        $replacementPattern = '/(' . $key . '=\S+)/';

        $this->data = preg_replace($replacementPattern, "{$key}={$value}\n", $this->data);

        return $this;
    }

    /**
     * Makes a new EnvFile instance.
     *
     * @param mixed ...$args
     *
     * @return EnvFile
     */
    public static function load(...$args)
    {
        return new static(...$args);
    }

    /**
     * When accessing the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Return paths repository.
     *
     * @return Repository
     */
    protected function paths(): Repository
    {
        return resolve(Repository::class);
    }
}
