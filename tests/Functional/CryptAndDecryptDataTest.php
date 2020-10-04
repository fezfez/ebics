<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Functional;

use Fezfez\Ebics\CertificateX509;
use Fezfez\Ebics\CertificatType;
use Fezfez\Ebics\Crypt\DecryptOrderDataContent;
use Fezfez\Ebics\Crypt\EncrytSignatureValueWithUserPrivateKey;
use Fezfez\Ebics\Crypt\FilterBlockedChar;
use Fezfez\Ebics\Crypt\GenerateCertificat;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\OrderDataEncrypted;
use Fezfez\Ebics\Password;
use Fezfez\Ebics\PrivateKey;
use Fezfez\Ebics\Tests\E2e\FakeCrypt;
use Fezfez\Ebics\UserCertificate;
use phpseclib\Crypt\AES;
use PHPUnit\Framework\TestCase;

use function gzcompress;

use const OPENSSL_ZERO_PADDING;

class CryptAndDecryptDataTest extends TestCase
{
    public function testFail(): void
    {
        $generateCert            = new GenerateCertificat();
        $encrypted               = new EncrytSignatureValueWithUserPrivateKey();
        $decryptOrderDataContent = new DecryptOrderDataContent();
        $password                = new Password('myPass');

        $xmlData = '<test><AuthenticationPubKeyInfo><X509Certificate>test</X509Certificate><Modulus>test</Modulus><Exponent>test</Exponent></AuthenticationPubKeyInfo><EncryptionPubKeyInfo><X509Certificate>test</X509Certificate><Modulus>test</Modulus><Exponent>test</Exponent></EncryptionPubKeyInfo></test>';

        $certE = new UserCertificate(CertificatType::e(), FakeCrypt::RSA_PUBLIC_KEY, new PrivateKey(FakeCrypt::RSA_PRIVATE_KEY), new CertificateX509(FakeCrypt::X509_PUBLIC));
        //$certE = $generateCert->__invoke(new SilarhiX509Generator(), $password, CertificatType::e());
        $transactionKey = $encrypted->__invoke($password, new PrivateKey($certE->getPublicKey()), $xmlData);

        $orderData = $this->aesCrypt((new FilterBlockedChar())->__invoke($xmlData), gzcompress($xmlData));

        $keyRing = new KeyRing('myPass');
        $keyRing->setUserCertificateEAndX($certE, $certE);

        self::assertXmlStringEqualsXmlString($xmlData, $decryptOrderDataContent->__invoke($keyRing, new OrderDataEncrypted($orderData, $transactionKey))->toString());
    }

    private function aesCrypt(string $key, string $cypher)
    {
        $aes = new AES(AES::MODE_CBC);
        $aes->setKeyLength(128);
        $aes->setKey($key);
        // Force openssl_options.
        $aes->openssl_options = OPENSSL_ZERO_PADDING;

        return $aes->encrypt($cypher);
    }
}
