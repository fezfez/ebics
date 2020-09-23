<?php

declare(strict_types=1);

namespace Cube43\Ebics\Crypt;

use Cube43\Ebics\BankCertificate;
use phpseclib\Crypt\RSA;
use RuntimeException;

use function base64_encode;
use function hash;
use function ltrim;
use function sprintf;
use function strlen;

class BankPublicKeyDigest
{
    public function __invoke(BankCertificate $certificate): string
    {
        $publicKey = new RSA();

        if ($publicKey->loadKey($certificate->getPublicKey()) === false) {
            throw new RuntimeException('unable to load key');
        }

        $e0 = $publicKey->exponent->toHex(true);
        $m0 = $publicKey->modulus->toHex(true);
        // If key was formed incorrect with Modulus and Exponent mismatch, then change the place of key parts.
        if (strlen($e0) > strlen($m0)) {
            $buffer = $e0;
            $e0     = $m0;
            $m0     = $buffer;
        }

        $e1   = ltrim($e0, '0');
        $m1   = ltrim($m0, '0');
        $key1 = sprintf('%s %s', $e1, $m1);

        return base64_encode(hash('sha256', $key1, true));
    }
}
