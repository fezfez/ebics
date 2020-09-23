<?php

declare(strict_types=1);

namespace Cube43\Ebics\Crypt;

use Cube43\Ebics\Password;
use Cube43\Ebics\PrivateKey;
use phpseclib\Crypt\RSA;
use RuntimeException;

use function define;
use function defined;

class EncrytSignatureValueWithUserPrivateKey
{
    private FilterBlockedChar $filterBlockedChar;

    public function __construct()
    {
        $this->filterBlockedChar = new FilterBlockedChar();
    }

    /**
     * Calculate signatureValue by encrypting Signature value with user Private key.
     *
     * @return string Base64 encoded
     *
     * @throws RuntimeException
     */
    public function __invoke(Password $password, PrivateKey $key, string $hash): string
    {
        $rsa = new RSA();
        $rsa->setPassword($password->value());
        $rsa->loadKey($key->value(), RSA::PRIVATE_FORMAT_PKCS1);

        if (! defined('CRYPT_RSA_PKCS15_COMPAT')) {
            define('CRYPT_RSA_PKCS15_COMPAT', true);
        }

        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $encrypted = $rsa->encrypt($this->filterBlockedChar->__invoke($hash));

        if (empty($encrypted)) {
            throw new RuntimeException('Incorrect authorization.');
        }

        return $encrypted;
    }
}
