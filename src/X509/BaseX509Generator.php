<?php

declare(strict_types=1);

namespace Fezfez\Ebics\X509;

use DateTimeImmutable;
use DateTimeInterface;
use Fezfez\Ebics\X509Generator;
use phpseclib\Crypt\RSA;
use phpseclib\File\X509;
use RuntimeException;

use function array_merge;
use function rand;
use function sprintf;
use function var_export;

/**
 * Default X509 certificate generator @see X509Generator.
 */
abstract class BaseX509Generator implements X509Generator
{
    protected DateTimeInterface $certificateStartDate;
    protected DateTimeInterface $certificateEndDate;
    protected string $serialNumber;

    public function __construct()
    {
        $this->certificateStartDate = (new DateTimeImmutable())->modify('-1 days');
        $this->certificateEndDate   = (new DateTimeImmutable())->modify('+1 year');
        $this->serialNumber         = $this->generateSerialNumber();
    }

    /**
     * Get certificate options
     *
     * @see X509 options
     *
     * @param array $options default generation options (may be empty)
     *
     * @return array the certificate options
     */
    abstract protected function getCertificateOptions(array $options = []): array;

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function generateX509(RSA $privateKey, RSA $publicKey, array $options = []): string
    {
        $options = array_merge([
            'subject' => [
                'domain' => null,
                'DN' => null,
            ],
            'issuer' => ['DN' => null], //Same as subject, means self-signed
            'extensions' => [],
        ], $this->getCertificateOptions($options));

        $subject = $this->generateSubject($publicKey, $options);
        $issuer  = $this->generateIssuer($privateKey, $publicKey, $subject, $options);

        $x509               = new X509();
        $x509->startDate    = $this->certificateStartDate->format('YmdHis');
        $x509->endDate      = $this->certificateEndDate->format('YmdHis');
        $x509->serialNumber = $this->serialNumber;

        $result = $x509->sign($issuer, $subject, 'sha256WithRSAEncryption');
        $x509->loadX509($result);

        foreach ($options['extensions'] as $id => $extension) {
            $extension = X509ExtensionOptionsNormalizer::normalize($extension);

            if ($x509->setExtension($id, $extension['value'], $extension['critical'], $extension['replace']) === false) {
                throw new RuntimeException(sprintf('Unable to set "%s" extension with value: %s', $id, var_export($extension['value'], true)));
            }
        }

        $result = $x509->sign($issuer, $x509, 'sha256WithRSAEncryption');

        return $x509->saveX509($result);
    }

    protected function generateSubject(RSA $publicKey, array $options): X509
    {
        $subject = new X509();
        $subject->setPublicKey($publicKey); // $pubKey is Crypt_RSA object

        if (! empty($options['subject']['DN'])) {
            $subject->setDN($options['subject']['DN']);
        }

        if (! empty($options['subject']['domain'])) {
            $subject->setDomain($options['subject']['domain']);
        }

        $subject->setKeyIdentifier($subject->computeKeyIdentifier($publicKey)); // id-ce-subjectKeyIdentifier

        return $subject;
    }

    protected function generateIssuer(RSA $privateKey, RSA $publicKey, X509 $subject, array $options): X509
    {
        $issuer = new X509();
        $issuer->setPrivateKey($privateKey); // $privKey is Crypt_RSA object

        if (! empty($options['issuer']['DN'])) {
            $issuer->setDN($options['issuer']['DN']);
        } else {
            $issuer->setDN($subject->getDN());
        }

        $issuer->setKeyIdentifier($subject->computeKeyIdentifier($publicKey));

        return $issuer;
    }

    /**
     * Generate 74 digits serial number represented in the string.
     */
    protected function generateSerialNumber(): string
    {
        // prevent the first number from being 0
        $result = rand(1, 9);
        for ($i = 0; $i < 74; ++$i) {
            $result .= rand(0, 9);
        }

        return (string) $result;
    }

    public function getCertificateStartDate(): DateTimeInterface
    {
        return $this->certificateStartDate;
    }

    public function getCertificateEndDate(): DateTimeInterface
    {
        return $this->certificateEndDate;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }
}
