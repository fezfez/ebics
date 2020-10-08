<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Command;

use DateTime;
use Fezfez\Ebics\Bank;
use Fezfez\Ebics\Crypt\BankPublicKeyDigest;
use Fezfez\Ebics\Crypt\DecryptOrderDataContent;
use Fezfez\Ebics\Crypt\EncrytSignatureValueWithUserPrivateKey;
use Fezfez\Ebics\DOMDocument;
use Fezfez\Ebics\EbicsServerCaller;
use Fezfez\Ebics\FDLParams;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\OrderDataEncrypted;
use Fezfez\Ebics\RenderXml;
use Fezfez\Ebics\User;
use phpseclib\Crypt\Random;

use function base64_decode;
use function base64_encode;
use function bin2hex;
use function hash;
use function strtoupper;

class FDLCommand
{
    private RenderXml $renderXml;
    private EbicsServerCaller $ebicsServerCaller;
    private EncrytSignatureValueWithUserPrivateKey $cryptStringWithPasswordAndCertificat;
    private DecryptOrderDataContent $decryptOrderDataContent;
    private BankPublicKeyDigest $bankPublicKeyDigest;

    public function __construct(
        ?EbicsServerCaller $ebicsServerCaller = null,
        ?EncrytSignatureValueWithUserPrivateKey $cryptStringWithPasswordAndCertificat = null,
        ?RenderXml $renderXml = null
    ) {
        $this->ebicsServerCaller                    = $ebicsServerCaller ?? new EbicsServerCaller();
        $this->cryptStringWithPasswordAndCertificat = $cryptStringWithPasswordAndCertificat ?? new EncrytSignatureValueWithUserPrivateKey();
        $this->renderXml                            = $renderXml ?? new RenderXml();
        $this->decryptOrderDataContent              = new DecryptOrderDataContent();
        $this->bankPublicKeyDigest                  = new BankPublicKeyDigest();
    }

    public function __invoke(Bank $bank, User $user, KeyRing $keyRing, FDLParams $FDLParams): string
    {
        $search = [
            '{{HostID}}' => $bank->getHostId(),
            '{{Nonce}}' => strtoupper(bin2hex(Random::string(16))),
            '{{Timestamp}}' => (new DateTime())->format('Y-m-d\TH:i:s\Z'),
            '{{PartnerID}}' => $user->getPartnerId(),
            '{{UserID}}' => $user->getUserId(),
            '{{BankPubKeyDigestsEncryption}}' => $this->bankPublicKeyDigest->__invoke($keyRing->getBankCertificateE()),
            '{{BankPubKeyDigestsAuthentication}}' => $this->bankPublicKeyDigest->__invoke($keyRing->getBankCertificateX()),
            '{{FileFormat}}' => $FDLParams->fileFormat(),
            '{{CountryCode}}' => $FDLParams->countryCode(),
        ];

        $search['{{rawDigest}}']         = $this->renderXml->renderXmlRaw($search, $bank->getVersion(), 'FDL_digest.xml');
        $search['{{DigestValue}}']       = base64_encode(hash('sha256', $search['{{rawDigest}}'], true));
        $search['{{RawSignatureValue}}'] = $this->renderXml->renderXmlRaw($search, $bank->getVersion(), 'FDL_SignatureValue.xml');
        $search['{{SignatureValue}}']    = base64_encode(
            $this->cryptStringWithPasswordAndCertificat->__invoke(
                $keyRing->getPassword(),
                $keyRing->getUserCertificateX()->getPrivateKey(),
                hash('sha256', $search['{{RawSignatureValue}}'], true)
            )
        );

        $ebicsServerResponse = new DOMDocument(
            $this->ebicsServerCaller->__invoke($this->renderXml->renderXmlRaw($search, $bank->getVersion(), 'FDL.xml'), $bank)
        );

        if ($ebicsServerResponse->getNodeValue('ReportText') === '[EBICS_OK] No download data available') {
            return '';
        }

        $decryptedResponse = $this->decryptOrderDataContent->__invoke(
            $keyRing,
            new OrderDataEncrypted(
                $ebicsServerResponse->getNodeValue('OrderData'),
                base64_decode($ebicsServerResponse->getNodeValue('TransactionKey'))
            )
        );

        return $decryptedResponse->getFormattedContent();
    }
}
