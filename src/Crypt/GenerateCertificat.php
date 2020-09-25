<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Crypt;

use Fezfez\Ebics\CertificateX509;
use Fezfez\Ebics\CertificatType;
use Fezfez\Ebics\Password;
use Fezfez\Ebics\PrivateKey;
use Fezfez\Ebics\UserCertificate;
use Fezfez\Ebics\X509Generator;
use phpseclib\Crypt\RSA;
use RuntimeException;

use function array_key_exists;
use function sprintf;

class GenerateCertificat
{
    public function __invoke(X509Generator $x509Generator, Password $password, CertificatType $type): UserCertificate
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
        $rsa->setPrivateKeyFormat(RSA::PUBLIC_FORMAT_PKCS1);
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');
        $rsa->setPassword($password->value());

        $keys = $rsa->createKey(2048);

        if (empty($keys)) {
            throw new RuntimeException(sprintf('key "publickey" does not exist for certificat type "%s"', $type->value()));
        }

        if (! array_key_exists('publickey', $keys) || empty($keys['publickey'])) {
            throw new RuntimeException(sprintf('key "publickey" does not exist for certificat type "%s"', $type->value()));
        }

        if (! array_key_exists('privatekey', $keys) || empty($keys['privatekey'])) {
            throw new RuntimeException(sprintf('key "privatekey" does not exist for certificat type "%s"', $type->value()));
        }

        $privateKey = new RSA();
        $privateKey->loadKey($keys['privatekey']);

        $publicKey = new RSA();
        $publicKey->loadKey($keys['publickey']);
        $publicKey->setPublicKey();

        return new UserCertificate(
            $type,
            $keys['publickey'],
            new PrivateKey($keys['privatekey']),
            new CertificateX509($x509Generator->generateX509($privateKey, $publicKey, ['type' => $type->value()]))
        );
    }
}
