<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

use RuntimeException;

class FDLParams
{
    private string $fileFormat;
    private string $countryCode;

    public function __construct(string $fileFormat, string $countryCode)
    {
        if (empty($fileFormat)) {
            throw new RuntimeException('fileFormat is empty');
        }

        if (empty($countryCode)) {
            throw new RuntimeException('countryCode is empty');
        }

        $this->fileFormat  = $fileFormat;
        $this->countryCode = $countryCode;
    }

    public function fileFormat(): string
    {
        return $this->fileFormat;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }
}
