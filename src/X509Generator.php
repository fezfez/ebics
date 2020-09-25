<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

use phpseclib\Crypt\RSA;

/**
 * X509 Factory Interface representation.
 */
interface X509Generator
{
    /**
     * Generate a X509 certificate and returns its content
     *
     * @param RSA   $privateKey the private key
     * @param RSA   $publicKey  the public key
     * @param array $options    optional generation options (may be empty)
     *
     * @return string the X509 content
     */
    public function generateX509(RSA $privateKey, RSA $publicKey, array $options = []): string;
}
