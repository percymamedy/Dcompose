<?php

namespace App\Support\Artifacts;

use App\Laradock;
use Symfony\Component\Yaml\Yaml;
use App\Support\Paths\Repository;
use Illuminate\Support\Facades\Storage;

class DockerComposeFile
{
    /**
     * Store of data to search from.
     *
     * @var array
     */
    protected $repositoryData;

    /**
     * The services choosen for creating the
     * docker-compose.yml file.
     *
     * @var array
     */
    protected $choosenServices;

    /**
     * Final contents of the new
     * docker-compose.yml file.
     *
     * @var array
     */
    protected $contents;

    /**
     * DockerComposeFile constructor.
     *
     * @param array $repositoryData
     * @param array $choosenServices
     * @param array $contents
     */
    public function __construct(array $repositoryData, array $choosenServices = [], array $contents = [])
    {
        $this->repositoryData = $repositoryData;
        $this->choosenServices = $choosenServices;
        $this->contents = $contents;
    }

    /**
     * Add common data to the docker-compose.yml.
     *
     * @return DockerComposeFile
     */
    public function addCommons(): DockerComposeFile
    {
        // Add Versions.
        $this->contents['version'] = $this->repositoryData['version'];
        // Add networks.
        $this->contents['networks'] = $this->repositoryData['networks'];

        // Add Volumes depending on services.
        foreach ($this->choosenServices as $service) {
            $this->addVolume($service);
        }

        // Add Docker in Docker service.
        $this->addService('docker-in-docker');

        return $this;
    }

    /**
     * Add sevices to the contents.
     *
     * @return DockerComposeFile
     */
    public function addServices(): DockerComposeFile
    {
        foreach ($this->choosenServices as $service) {
            $this->addService($service);
        }

        return $this;
    }

    /**
     * Add a service to the contents.
     *
     * @param string $service
     * @param bool   $withVolume
     *
     * @return DockerComposeFile
     */
    public function addService(string $service, $withVolume = false): DockerComposeFile
    {
        // Checks if we need to add a volume before adding
        // the service.
        if ($withVolume) {
            $this->addVolume($service);
        }

        // Checks if the repository data contains the service
        // and if so add to the new contents.
        if (array_key_exists($service, $this->repositoryData['services'])) {
            $this->contents['services'][$service] = $this->changeContext(
                $this->repositoryData['services'][$service]
            );
        }

        return $this;
    }

    /**
     * Add volume to a service.
     *
     * @param string $service
     *
     * @return DockerComposeFile
     */
    public function addVolume(string $service): DockerComposeFile
    {
        // Check if the service should contain a volume and if so
        // add it.
        if (array_key_exists($service, $this->repositoryData['volumes'])) {
            $this->contents['volumes'][$service] = $this->repositoryData['volumes'][$service];
        }

        return $this;
    }

    /**
     * Remove a volume according to the service.
     *
     * @param string $service
     *
     * @return DockerComposeFile
     */
    public function removeVolume(string $service): DockerComposeFile
    {
        // Checks if the docker-compose.yml file has this service
        // volume.
        if (array_key_exists($service, $this->contents['volumes'])) {
            $this->cleanVolumes($service);
        }

        return $this;
    }

    /**
     * Remove a service.
     *
     * @param string $service
     * @param bool   $withVolume
     *
     * @return DockerComposeFile
     */
    public function removeService(string $service, $withVolume = false): DockerComposeFile
    {
        // Checks if we need to remove a volume before removing
        // the service.
        if ($withVolume) {
            $this->removeVolume($service);
        }

        // Checks if the docker-compose.yml file has this service
        // and then if so remove the service.
        if (array_key_exists($service, $this->repositoryData['services'])) {
            $this->cleanServices($service);
        }

        return $this;
    }

    /**
     * Renders the docker-compose.yml file.
     *
     * @return string
     */
    public function render(): string
    {
        return Yaml::dump($this->contents, 6);
    }

    /**
     * Save the docker-compose file to disk.
     *
     * @return bool
     */
    public function persist(): bool
    {
        return Storage::disk('work_dir')->put(
            $this->paths()->dockerComposePath,
            $this->render()
        );
    }

    /**
     * Checks if the Service exists in the docker-compose.yml file.
     *
     * @param string $service
     *
     * @return bool
     */
    public function exists(string $service): bool
    {
        return array_key_exists($service, $this->contents['services']);
    }

    /**
     * Create a new instance of DockerCompose file.
     *
     * @param array $repositoryData
     * @param array $choosenServices
     *
     * @return DockerComposeFile
     */
    public static function make(array $repositoryData, array $choosenServices)
    {
        return new static($repositoryData, $choosenServices);
    }

    /**
     * Load a new DockerCompose from current docker-compose.yml file.
     *
     * @return DockerComposeFile
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function load(): DockerComposeFile
    {
        // Get current docker-compose.yml contents.
        $contents = Yaml::parse(
            Storage::disk('work_dir')->get(resolve(Repository::class)->dockerComposePath)
        );

        // Get choosen services.
        $choosenServices = isset($contents['services']) ? array_keys($contents['services']) : [];

        return new static(
            resolve(Laradock::class)->dockerComposeData(),
            $choosenServices,
            $contents
        );
    }

    /**
     * Change the build context for a Service if needed.
     *
     * @param array  $serviceData
     * @param string $contentFolder
     *
     * @return array
     */
    protected function changeContext(array $serviceData, string $contentFolder = './.docker/'): array
    {
        // Change current build folder to .docker/ folder.
        if (isset($serviceData['build']['context']) && !is_array($serviceData['build']['context'])) {
            $serviceData['build']['context'] = $contentFolder . ltrim($serviceData['build']['context'], './');
        }

        // Change current build folder to .docker/ folder.
        if (isset($serviceData['build']) && !is_array($serviceData['build'])) {
            $serviceData['build'] = $contentFolder . ltrim($serviceData['build'], './');
        }

        // Change volumes folder to .docker/ folder.
        if (isset($serviceData['volumes']) && is_array($serviceData['volumes'])) {
            foreach ($serviceData['volumes'] as $key => $volume) {
                $serviceData['volumes'][$key] = str_replace('./', './.docker/', $serviceData['volumes'][$key]);
            }
        }

        return $serviceData;
    }

    /**
     * Return the path repository instance.
     *
     * @return Repository
     */
    protected function paths(): Repository
    {
        return resolve(Repository::class);
    }

    /**
     * Remove volume from the docker-compose.yml file.
     *
     * @param string $service
     */
    protected function cleanVolumes(string $service)
    {
        unset($this->contents['volumes'][$service]);

        if (empty($this->contents['volumes'])) {
            unset($this->contents['volumes']);
        }
    }

    /**
     * Remove services from docker-compose.yml file.
     *
     * @param string $service
     */
    protected function cleanServices(string $service)
    {
        unset($this->contents['services'][$service]);

        if (empty($this->contents['services'])) {
            unset($this->contents['services']);
        }
    }
}
