<?php

namespace App\Support\Artifacts;

use Symfony\Component\Yaml\Yaml;

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
     */
    public function __construct(array $repositoryData, array $choosenServices)
    {
        $this->repositoryData = $repositoryData;
        $this->choosenServices = $choosenServices;
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
            // Check if the service should contain a volume.
            if (array_key_exists($service, $this->repositoryData['volumes'])) {
                $this->contents['volumes'][$service] = $this->repositoryData['volumes'][$service];
            }
        }

        // Add Docker in Docker service.
        $this->contents['services']['docker-in-docker'] = $this->repositoryData['services']['docker-in-docker'];

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
            // Add each Service to the contents.
            if (array_key_exists($service, $this->repositoryData['services'])) {
                $this->contents['services'][$service] = $this->changeContext(
                    $this->repositoryData['services'][$service]
                );
            }
        }

        return $this;
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
     * Renders the docker-compose.yml file.
     *
     * @return string
     */
    public function render(): string
    {
        return Yaml::dump($this->contents, 6);
    }

    /**
     * Creae a new instance of DockerCompose file.
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
}
