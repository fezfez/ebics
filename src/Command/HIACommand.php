<?php

declare(strict_types=1);

namespace Cube43\Ebics\Command;

use Cube43\Ebics\Bank;
use Cube43\Ebics\CertificatType;
use Cube43\Ebics\Crypt\GenerateCertificat;
use Cube43\Ebics\EbicsServerCaller;
use Cube43\Ebics\KeyRing;
use Cube43\Ebics\RenderXml;
use Cube43\Ebics\User;
use Cube43\Ebics\X509Generator;
use DateTime;

use function base64_encode;
use function Safe\gzcompress;

class HIACommand
{
    private EbicsServerCaller $httpClient;
    private GenerateCertificat $generateCertificat;
    private RenderXml $renderXml;

    public function __construct(
        ?EbicsServerCaller $httpClient = null,
        ?GenerateCertificat $generateCertificat = null,
        ?RenderXml $renderXml = null
    ) {
        $this->httpClient         = $httpClient ?? new EbicsServerCaller();
        $this->generateCertificat = $generateCertificat ?? new GenerateCertificat();
        $this->renderXml          = $renderXml ?? new RenderXml();
    }

    public function __invoke(Bank $bank, User $user, KeyRing $keyRing, X509Generator $x509Generator): KeyRing
    {
        $keyRing->setUserCertificateE($this->generateCertificat->__invoke($x509Generator, $keyRing->getPassword(), CertificatType::e()));
        $keyRing->setUserCertificateX($this->generateCertificat->__invoke($x509Generator, $keyRing->getPassword(), CertificatType::x()));

        $search = [
            '{{TimeStamp}}' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
            '{{CertUserE_Modulus}}' => base64_encode($keyRing->getUserCertificateE()->getPublicKeyDetails()->getModulus()),
            '{{CertUserE_Exponent}}' => base64_encode($keyRing->getUserCertificateE()->getPublicKeyDetails()->getExponent()),
            '{{CertUserE_X509IssuerName}}' => $keyRing->getUserCertificateE()->getCertificatX509()->getInsurerName(),
            '{{CertUserE_X509SerialNumber}}' => $keyRing->getUserCertificateE()->getCertificatX509()->getSerialNumber(),
            '{{CertUserE_X509Certificate}}' => base64_encode($keyRing->getUserCertificateE()->getCertificatX509()->value()),
            '{{CertUserX_Modulus}}' => base64_encode($keyRing->getUserCertificateX()->getPublicKeyDetails()->getModulus()),
            '{{CertUserX_Exponent}}' => base64_encode($keyRing->getUserCertificateX()->getPublicKeyDetails()->getExponent()),
            '{{CertUserX_X509IssuerName}}' => $keyRing->getUserCertificateX()->getCertificatX509()->getInsurerName(),
            '{{CertUserX_X509SerialNumber}}' => $keyRing->getUserCertificateX()->getCertificatX509()->getSerialNumber(),
            '{{CertUserX_X509Certificate}}' => base64_encode($keyRing->getUserCertificateX()->getCertificatX509()->value()),
            '{{PartnerID}}' => $user->getPartnerId(),
            '{{UserID}}' => $user->getUserId(),
            '{{HostID}}' => $bank->getHostId(),
        ];

        $search['{{OrderData}}'] = base64_encode(gzcompress($this->renderXml->__invoke($search, $bank->getVersion(), 'HIA_OrderData.xml')->toString()));

        $this->httpClient->__invoke($this->renderXml->__invoke($search, $bank->getVersion(), 'HIA.xml')->toString(), $bank);

        return $keyRing;
    }
}
