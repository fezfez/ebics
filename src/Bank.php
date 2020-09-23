<?php

declare(strict_types=1);

namespace Cube43\Ebics;

class Bank
{
    private string $hostId;
    private string $url;
    private Version $version;

    public function __construct(string $hostId, string $url, Version $version)
    {
        $this->hostId  = $hostId;
        $this->url     = $url;
        $this->version = $version;
    }

    public function getHostId(): string
    {
        return $this->hostId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isCertified(): bool
    {
        return true;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
