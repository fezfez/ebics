<?php

declare(strict_types=1);

namespace Fezfez\Ebics;

use phpseclib\Crypt\RSA;
use phpseclib\File\X509;
use RuntimeException;

use function array_map;
use function array_shift;
use function implode;
use function openssl_x509_fingerprint;
use function str_split;
use function strtoupper;
use function wordwrap;

/**
 * Class CertificateX509 represents Certificate model in X509 structure.
 *
 * @method RSA getPublicKey()
 */
class CertificateX509 extends X509
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new RuntimeException('x509 key is empty');
        }

        parent::__construct();

        $this->loadX509($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function digest(): string
    {
        $fingerPrint = openssl_x509_fingerprint($this->value, 'sha256');

        if ($fingerPrint === false) {
            throw new RuntimeException('unable to calculate fingerprint');
        }

        $digest  = strtoupper($fingerPrint);
        $digests = str_split($digest, 16);
        $digests = array_map(static function ($digest) {
            return wordwrap($digest, 2, ' ', true);
        }, $digests);

        return implode("\n", $digests);
    }

    /**
     * Get Certificate serialNumber.
     */
    public function getSerialNumber(): string
    {
        $certificateSerialNumber = $this->currentCert['tbsCertificate']['serialNumber'];

        return $certificateSerialNumber->toString();
    }

    /**
     * Get Certificate Issuer DN property id-at-commonName.
     */
    public function getInsurerName(): string
    {
        $certificateInsurerName = $this->getIssuerDNProp('id-at-commonName');

        return array_shift($certificateInsurerName);
    }
}
