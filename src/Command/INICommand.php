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

class INICommand
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
        $keyRing->setUserCertificateA($this->generateCertificat->__invoke($x509Generator, $keyRing->getPassword(), CertificatType::a()));

        $search = [
            '{{TimeStamp}}' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
            '{{Modulus}}' => base64_encode($keyRing->getUserCertificateA()->getPublicKeyDetails()->getModulus()),
            '{{Exponent}}' => base64_encode($keyRing->getUserCertificateA()->getPublicKeyDetails()->getExponent()),
            '{{X509IssuerName}}' => $keyRing->getUserCertificateA()->getCertificatX509()->getInsurerName(),
            '{{X509SerialNumber}}' => $keyRing->getUserCertificateA()->getCertificatX509()->getSerialNumber(),
            '{{X509Certificate}}' => base64_encode($keyRing->getUserCertificateA()->getCertificatX509()->value()),
            '{{PartnerID}}' => $user->getPartnerId(),
            '{{UserID}}' => $user->getUserId(),
            '{{HostID}}' => $bank->getHostId(),
        ];

        $search['{{OrderData}}'] = base64_encode(gzcompress($this->renderXml->__invoke($search, $bank->getVersion(), 'INI_OrderData.xml')->toString()));

        $this->httpClient->__invoke($this->renderXml->__invoke($search, $bank->getVersion(), 'INI.xml')->toString(), $bank);

        return $keyRing;
    }
}
