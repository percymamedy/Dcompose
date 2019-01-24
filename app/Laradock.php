<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Laradock
{
    /**
     * GitHub API client.
     *
     * @var Client
     */
    protected $gitHubClient;

    /**
     * The laradock zip path.
     *
     * @var string
     */
    protected $laradockZip;

    /**
     * Satchel instance.
     *
     * @var Satchel
     */
    protected $satchel;

    /**
     * Laradock constructor.
     *
     * @param Client  $gitHubClient
     * @param string  $laradockZip
     * @param Satchel $satchel
     */
    public function __construct(Client $gitHubClient, string $laradockZip, Satchel $satchel)
    {
        $this->laradockZip = $laradockZip;
        $this->gitHubClient = $gitHubClient;
        $this->satchel = $satchel;
    }

    /**
     * Download and unzip laradock.
     *
     * @return Laradock
     */
    public function grabAndPutInSatchel(): Laradock
    {
        return $this->downloadZip()->unzip();
    }

    /**
     * Get available services.
     *
     * @return Collection
     */
    public function availableServices(): Collection
    {
        return $this->satchel->laradockData();
    }

    /**
     * Unzip Laradock zip file.
     *
     * @return Laradock
     */
    protected function unzip(): Laradock
    {
        $zip = new \ZipArchive();
        $resource = $zip->open($this->laradockZip);

        // Check if we were able to get
        // resource.
        if ($resource === true) {
            // Extract the archive.
            $zip->extractTo(home_dir() . DIRECTORY_SEPARATOR . 'laradock');
            // Rename extracted folder so we may access it later.
            $this->satchel->move(
                array_first($this->satchel->directories('laradock')),
                'laradock/data'
            );
            return $this;
        }

        throw new \InvalidArgumentException('Cannot open laradock zip');
    }

    /**
     * Downloads the laradock zip.
     *
     * @return self
     */
    protected function downloadZip(): Laradock
    {
        $this->gitHubClient->get('/repos/laradock/laradock/zipball', ['sink' => $this->laradockZip]);

        return $this;
    }
}
