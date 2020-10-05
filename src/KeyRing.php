<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

use JsonSerializable;
use RuntimeException;

use function array_key_exists;
use function is_file;
use function Safe\file_get_contents;
use function Safe\json_decode;

class KeyRing implements JsonSerializable
{
    private ?UserCertificate $userCertificateA = null;
    private ?UserCertificate $userCertificateX = null;
    private ?UserCertificate $userCertificateE = null;
    private ?BankCertificate $bankCertificateX = null;
    private ?BankCertificate $bankCertificateE = null;

    private Password $password;

    public function __construct(string $password)
    {
        $this->password = new Password($password);
    }

    public function setUserCertificateA(UserCertificate $certificate): void
    {
        if ($this->userCertificateA !== null) {
            throw new RuntimeException('userCertificateA already exist');
        }

        $this->userCertificateA = $certificate;
    }

    public function setUserCertificateEAndX(UserCertificate $userCertificateE, UserCertificate $userCertificateX): void
    {
        if ($this->userCertificateE !== null) {
            throw new RuntimeException('userCertificateE already exist');
        }

        if ($this->userCertificateX !== null) {
            throw new RuntimeException('userCertificateX already exist');
        }

        $this->userCertificateE = $userCertificateE;
        $this->userCertificateX = $userCertificateX;
    }

    public function setBankCertificate(BankCertificate $bankCertificateX, BankCertificate $bankCertificateE): void
    {
        if ($this->bankCertificateX !== null) {
            throw new RuntimeException('bankCertificateX already exist');
        }

        if ($this->bankCertificateE !== null) {
            throw new RuntimeException('bankCertificateE already exist');
        }

        $this->bankCertificateX = $bankCertificateX;
        $this->bankCertificateE = $bankCertificateE;
    }

    public function getUserCertificateA(): UserCertificate
    {
        if ($this->userCertificateA === null) {
            throw new RuntimeException('userCertificateA empty');
        }

        return $this->userCertificateA;
    }

    public function getUserCertificateX(): UserCertificate
    {
        if ($this->userCertificateX === null) {
            throw new RuntimeException('userCertificateX empty');
        }

        return $this->userCertificateX;
    }

    public function getUserCertificateE(): UserCertificate
    {
        if ($this->userCertificateE === null) {
            throw new RuntimeException('userCertificateE empty');
        }

        return $this->userCertificateE;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getBankCertificateX(): BankCertificate
    {
        if ($this->bankCertificateX === null) {
            throw new RuntimeException('bankCertificateX empty');
        }

        return $this->bankCertificateX;
    }

    public function getBankCertificateE(): BankCertificate
    {
        if ($this->bankCertificateE === null) {
            throw new RuntimeException('bankCertificateE empty');
        }

        return $this->bankCertificateE;
    }

    public static function fromFile(string $file, string $password): self
    {
        if (! is_file($file)) {
            return new self($password);
        }

        return self::fromArray(json_decode(file_get_contents($file), true), $password);
    }

    public static function fromArray(array $data, string $password): self
    {
        $keyring = new self($password);

        $buildBankCertificate = static function (string $key) use ($data): ?BankCertificate {
            if (array_key_exists($key, $data) && ! empty($data[$key])) {
                return BankCertificate::fromArray($data[$key]);
            }

            return null;
        };

        $buildUserCertificate = static function (string $key) use ($data): ?UserCertificate {
            if (array_key_exists($key, $data) && ! empty($data[$key])) {
                return UserCertificate::fromArray($data[$key]);
            }

            return null;
        };

        $keyring->bankCertificateE = $buildBankCertificate('bankCertificateE');
        $keyring->bankCertificateX = $buildBankCertificate('bankCertificateX');
        $keyring->userCertificateA = $buildUserCertificate('userCertificateA');
        $keyring->userCertificateE = $buildUserCertificate('userCertificateE');
        $keyring->userCertificateX = $buildUserCertificate('userCertificateX');

        return $keyring;
    }

    public function jsonSerialize(): array
    {
        return [
            'bankCertificateE' => $this->bankCertificateE,
            'bankCertificateX' => $this->bankCertificateX,
            'userCertificateA' => $this->userCertificateA,
            'userCertificateE' => $this->userCertificateE,
            'userCertificateX' => $this->userCertificateX,
        ];
    }
}
