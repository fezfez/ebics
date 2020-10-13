<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\E2e\Command;

use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use Fezfez\Ebics\Bank;
use Fezfez\Ebics\BankCertificate;
use Fezfez\Ebics\CertificateX509;
use Fezfez\Ebics\CertificatType;
use Fezfez\Ebics\Command\FDLCommand;
use Fezfez\Ebics\Crypt\AddRsaSha256PrefixAndReturnAsBinary;
use Fezfez\Ebics\EbicsServerCaller;
use Fezfez\Ebics\FDLParams;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\PrivateKey;
use Fezfez\Ebics\Tests\E2e\FakeCrypt;
use Fezfez\Ebics\User;
use Fezfez\Ebics\UserCertificate;
use Fezfez\Ebics\Version;
use phpseclib\Crypt\RSA;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use XmlValidator\XmlValidator;

use function base64_decode;
use function base64_encode;
use function bin2hex;
use function define;
use function defined;
use function hash;
use function print_r;

class FDLCommandTest extends TestCase
{
    private function getCallback(string $response, Version $version, callable $requestCallback): callable
    {
        return static function ($method, $url, $options) use ($response, $version, $requestCallback) {
            $versionToXsd = [
                Version::v24()->value() => __DIR__ . '/../xsd/24/H003/ebics.xsd',
                Version::v25()->value()  => __DIR__ . '/../xsd/25/ebics_H004.xsd',
                Version::v30()->value()  => __DIR__ . '/../xsd/30/ebics_H005.xsd',
            ];

            $xmlValidator = new XmlValidator();
            $isValid      = $xmlValidator->validate($options['body'], $versionToXsd[$version->value()]);

            self::assertTrue($isValid, print_r($xmlValidator->errors, true));

            $requestCallback($options['body']);

            $xmlValidator = new XmlValidator();
            $isValid      = $xmlValidator->validate($response, $versionToXsd[$version->value()]);

            self::assertTrue($isValid, print_r($xmlValidator->errors, true));

            return new MockResponse($response);
        };
    }

    public function provideVersion(): iterable
    {
        yield [Version::v24()];
        yield [Version::v25()];
    }

    /** @dataProvider provideVersion */
    public function testOk(Version $version): void
    {
        $tocheck = static function (string $response): void {
            $xml = new DOMDocument();
            $xml->loadXML($response);

            $digestOk = static function (string $rawdigest, string $digestValue) {
                return bin2hex(base64_decode($digestValue)) === hash('sha256', $rawdigest);
            };

            $crpyt = static function ($ciphertext) {
                $rsa = new RSA();
                $rsa->setPassword('');
                $rsa->loadKey(FakeCrypt::RSA_PRIVATE_KEY, RSA::PRIVATE_FORMAT_PKCS1);

                if (! defined('CRYPT_RSA_PKCS15_COMPAT')) {
                    define('CRYPT_RSA_PKCS15_COMPAT', true);
                }

                $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);

                return $rsa->encrypt((new AddRsaSha256PrefixAndReturnAsBinary())->__invoke($ciphertext));
            };

            $signatureOk = static function ($signatureRaw, $signatureValue) use ($crpyt) {
                return base64_encode($crpyt(hash('sha256', $signatureRaw, true))) === $signatureValue;
            };

            $findElement = static function (DOMDocument $xml, string $nodeName): DOMNode {
                $node = $xml->getElementsByTagName($nodeName)->item(0);

                if ($node === null) {
                    throw new RuntimeException('node not found');
                }

                return $node;
            };

            self::assertTrue($digestOk($findElement($xml, 'header')->C14N(), $findElement($xml, 'DigestValue')->nodeValue));
            self::assertTrue($signatureOk($findElement($xml, 'SignedInfo')->C14N(), $findElement($xml, 'SignatureValue')->nodeValue));
        };

        $tkey  = 'uBrH173GUziiFUQLBQ7MmlCVCoUqOSxj08hEfiSAxkv9RW2uFJes4jXvn1CVD9Kfa0ot8nG7QIb8aWKaix3XdPFbG5gSbZIk2bGowj5FsijwkCDiBFzSsJhpHskIq2crLDk5c4LzVXrEQBJvUIoQ70OdXzJc8/nhThhkG8hJgGMJH35we0JCqzTcQP8DsdjtApX+HN1UnCdPsmhU2vXR2BpvIDgIluJT/dnzWfp5mhfaGKIMA3+Ow+EEuzrwY8JRAP/P9RYyfptjdsNVwUgb9X6xgAkV805JhIf7g9L3GvJjA1/jhYL2Xj97YC+4dWdswe4WTlrJ+3MPA44Dk3zxrwzv+Iu/66PsAboeW8HB7QEXK6AXxEZq0h6Ng2wSfwJSkZE9UU5xUcFG2S/e41M23ZSBMD/mMy5yadPLhQQ3QBP3bwfgee4bnPky1hwN60yUZdaHvF3z92pStV7GCmxcF9Gt420LGciJ2A9yWDpsxtalmLHzozsIeC687WsOzxN/';
        $odata = '8jGZE4A8/CEsmzl4kBNVcbDm+QmBpAMtZhCspu8sSL4GxDBmEEj06Yr+8L30bf6TjtSOJiDeeqnnakVCUvTy2YJMTY8aaSF+OwE/iEclqyRtayCjXxkt/073WwPWlE7P0rRrLzGW/n7BCRJW3ffuMw==';

        $v24 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<ebicsResponse xmlns="http://www.ebics.org/H003" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="H003" xsi:schemaLocation="http://www.ebics.org/H003 http://www.ebics.org/H003/ebics_response.xsd">
    <header authenticate="true">
        <static>
            <TransactionID>4306ABF98C968ACD32508E0C6D9DC741</TransactionID>
            <NumSegments>1</NumSegments>
        </static>
        <mutable>
            <TransactionPhase>Initialisation</TransactionPhase>
            <SegmentNumber lastSegment="true">1</SegmentNumber>
            <ReturnCode>000000</ReturnCode>
            <ReportText>[EBICS_OK] OK</ReportText>
        </mutable>
    </header>
    
    <AuthSignature><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/><ds:Reference URI="#xpointer(//*[@authenticate=\'true\'])"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><ds:DigestValue></ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue></ds:SignatureValue></AuthSignature><body>
        <DataTransfer>
            <DataEncryptionInfo authenticate="true">
                <EncryptionPubKeyDigest Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" Version="E002"></EncryptionPubKeyDigest>
                <TransactionKey>' . $tkey . '</TransactionKey>
            </DataEncryptionInfo>
            <OrderData>' . $odata . '</OrderData>
        </DataTransfer>
        <ReturnCode authenticate="true">000000</ReturnCode>
    </body>
</ebicsResponse>
';

        $v25 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<ebicsResponse xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="H003" xsi:schemaLocation="urn:org:ebics:H004 ebics_keymgmt_response_H004.xsd">
    <header authenticate="true">
        <static>
            <TransactionID>4306ABF98C968ACD32508E0C6D9DC741</TransactionID>
            <NumSegments>1</NumSegments>
        </static>
        <mutable>
            <TransactionPhase>Initialisation</TransactionPhase>
            <SegmentNumber lastSegment="true">1</SegmentNumber>
            <ReturnCode>000000</ReturnCode>
            <ReportText>[EBICS_OK] OK</ReportText>
        </mutable>
    </header>
    
    <AuthSignature><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/><ds:Reference URI="#xpointer(//*[@authenticate=\'true\'])"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><ds:DigestValue></ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue></ds:SignatureValue></AuthSignature><body>
        <DataTransfer>
            <DataEncryptionInfo authenticate="true">
                <EncryptionPubKeyDigest Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" Version="E002"></EncryptionPubKeyDigest>
                <TransactionKey>' . $tkey . '</TransactionKey>
            </DataEncryptionInfo>
            <OrderData>' . $odata . '</OrderData>
        </DataTransfer>
        <ReturnCode authenticate="true">000000</ReturnCode>
    </body>
</ebicsResponse>
';

        $versionToXmlResponse = [
            Version::v24()->value() => $v24,
            Version::v25()->value() => $v25,
        ];

        $sUT = new FDLCommand(
            new EbicsServerCaller(new MockHttpClient($this->getCallback($versionToXmlResponse[$version->value()], $version, $tocheck)))
        );

        $bank    = new Bank('myHostId', 'http://myurl.com', $version);
        $user    = new User('myPartId', 'myUserId');
        $keyRing = new KeyRing('');

        $keyRing->setUserCertificateEAndX(
            new UserCertificate(
                CertificatType::e(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new PrivateKey(FakeCrypt::RSA_PRIVATE_KEY),
                new CertificateX509(FakeCrypt::RSA_PUBLIC_KEY)
            ),
            new UserCertificate(
                CertificatType::x(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new PrivateKey(FakeCrypt::RSA_PRIVATE_KEY),
                new CertificateX509(FakeCrypt::RSA_PUBLIC_KEY)
            )
        );
        $keyRing->setBankCertificate(
            new BankCertificate(
                CertificatType::x(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new CertificateX509(FakeCrypt::RSA_PUBLIC_KEY)
            ),
            new BankCertificate(
                CertificatType::e(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new CertificateX509(FakeCrypt::RSA_PUBLIC_KEY)
            ),
        );

        $sUT->__invoke($bank, $user, $keyRing, new FDLParams('test', 'FR', new DateTimeImmutable(), new DateTimeImmutable()), static function (string $data): void {
            self::assertSame($data, '<test><AuthenticationPubKeyInfo><X509Certificate>test</X509Certificate><Modulus>test</Modulus><Exponent>test</Exponent></AuthenticationPubKeyInfo><EncryptionPubKeyInfo><X509Certificate>test</X509Certificate><Modulus>test</Modulus><Exponent>test</Exponent></EncryptionPubKeyInfo></test>');
        });
    }
}
