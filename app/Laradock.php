<?php

namespace App;

use GuzzleHttp\Client;

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
     * Laradock constructor.
     *
     * @param Client $gitHubClient
     * @param string $laradockZip
     */
    public function __construct(Client $gitHubClient, string $laradockZip)
    {
        $this->laradockZip = $laradockZip;
        $this->gitHubClient = $gitHubClient;
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
     * Unzip Laradock zip file.
     *
     * @return Laradock
     */
    protected function unzip(): Laradock
    {
        $zip = new \ZipArchive();
        $resource = $zip->open($this->laradockZip);

        if ($resource === true) {
            $zip->extractTo(home_dir() . DIRECTORY_SEPARATOR . 'laradock');
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
