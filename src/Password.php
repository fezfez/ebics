<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

class Password
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
