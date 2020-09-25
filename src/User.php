<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

class User
{
    private string $partnerId;
    private string $userId;

    public function __construct(string $partnerId, string $userId)
    {
        $this->partnerId = $partnerId;
        $this->userId    = $userId;
    }

    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
