<?php

declare(strict_types=1);

namespace Cube43\Ebics\E2e\Command;

use Cube43\Ebics\Bank;
use Cube43\Ebics\BankCertificate;
use Cube43\Ebics\CertificateX509;
use Cube43\Ebics\CertificatType;
use Cube43\Ebics\Command\FDLCommand;
use Cube43\Ebics\Crypt\FilterBlockedChar;
use Cube43\Ebics\EbicsServerCaller;
use Cube43\Ebics\KeyRing;
use Cube43\Ebics\PrivateKey;
use Cube43\Ebics\Tests\E2e\FakeCrypt;
use Cube43\Ebics\User;
use Cube43\Ebics\UserCertificate;
use Cube43\Ebics\Version;
use DOMDocument;
use DOMNode;
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

/*
            $xmlValidator = new XmlValidator();
            $isValid      = $xmlValidator->validate($response, $versionToXsd);

            self::assertTrue($isValid, print_r($xmlValidator->errors, true));
*/
            return new MockResponse($response);
        };
    }

    public function provideVersion(): iterable
    {
        yield [Version::v24()];
        yield [Version::v25()];
        yield [Version::v30()];
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

                return $rsa->encrypt((new FilterBlockedChar())->__invoke($ciphertext));
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

        $sUT = new FDLCommand(
            new EbicsServerCaller(new MockHttpClient($this->getCallback('<?xml version="1.0" encoding="UTF-8"?><ebicsKeyManagementResponse xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Revision="1" Version="H004" xsi:schemaLocation="urn:org:ebics:H004 ebics_keymgmt_response_H004.xsd"><header authenticate="true"><static/><mutable><OrderID>A05Y</OrderID><ReturnCode>000000</ReturnCode><ReportText>[EBICS_OK] OK</ReportText></mutable></header><body><DataTransfer><DataEncryptionInfo authenticate="true"><EncryptionPubKeyDigest Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" Version="E002">kWJ3YXAUrfQTbtJRQ5XM1CrN1LbifEAVpo77BYpXEv0=</EncryptionPubKeyDigest><TransactionKey>TU7dfDbB9QCa3L42O5l0KYXScHrcQmMJTZ58ctTW8zKZ3CSO3DmX4HG08/FgjCJGy6KZ2tG+M9wlM6RFDnXXP7e/DJNDIN8ixFzWPT7T4KyojSXToft3Xtynd+4EESlMRHWKjpn6zjFN0oCbU0BspeBkbb4sJJSAgebz6wYC0E4BwauplY46addt7uW4MeM1WW9si8Tbib59duDF40AspBQuKL2uTpIH19EXBrD1RIVCGJrpomCCOPNw8BpqM5fJnIzobibOqUgCti8+KpwB41p7vyU3XhGVnFY7wm6Oz5S885FPb93IHwc/3rIG3HCUrFrgy1SAbouwzDMLfHBfEg==</TransactionKey></DataEncryptionInfo><OrderData>kienRa8IbKMoS7gY+KlAptRlTwg+pfctXP4jHCJWsmt/o1AtPAXzUyzSuF+c8P7WIlitgKDCz46U5Qvyc2TAF7JsPZwsOsRcVWAkr4DSedhXOGDcJSfs8ZoMV0aNkchyXgdTh3oRAL0BksZJ+CSgCpxfueHMeb73ESLmfKXfRMyoTy6Fne7axFdT/WbjtrsuVHBxo44rBq2+Z4faPoLYAjr7y5i90nTbwPYskVFDZJM9fl6zyGTSvRuSGin0rcj/6d3e8BYG+tAufhqKORSPbKts+kr1/sNsPx6A+krJfVhleOD3bwIgDr13Y3V9JIi6L1DHAj6bEp8WZWsLdtK0DyPNFMN9oCRovp76dZgPlOe8rGFKKxd8Ul9eB8BcGGdwdzbAu8OuSh2/MiwAFQ5i8REUAuNlqNlyeCv4KSeyjDi6c+Ii84BeYElOZICG8+g63K0sCRMKtTnav8dIZfkg5krxUntXbju1dRU2XXCM6nJyDQPnazUB6BTc+5awWmfAbH6tWLyMkcJaazq2l0G1zlqw9sN+bMtNp4guenXGII0U7SmdoMGHq3OjYJ17W4JDxRgncvQTt+zMoUiLnHpJp8ba1yw8FBh1fax8OjMFKIEom9lFxAOQBiHsrjXsQfxMUgziQKSbboWxKounFd/aeYZjs68udC3TWkcFbaUIrlL17UaGEMalYdfPlUkjq4OGwXdIi3xsfIQMwCmid7C2S0pr6F+u1AVbLV1ld6kqdmswtqDnNkfgnb0vrb3EFaAJNdT5/s2Hy1r+Kfg75oVoKua1l6xr7vyp5lpfBBrS8cOY5zEGIfjqQVegoNepdOc6fK4D5zia4AVv2yBqGKfS28Xi20S7WzF2661IFKOfpUwPjBI1J3JuT09aEGCPKieV+6px9ysYCL4AahjSflCASi3GlRbLQPRhmQZKJ14rFyKDMMSnsv1e5Hn1TLhgMbOqQwC6HyZwiyvOxRuK6I6Kk9d3sOyxFhnZdYuT7cIdwvZPOgtI+p6NmRcdWIWsN4IuxZ9i0z2ZWQoVubXX3qjGZStxOcUz1fHD78ggUOzBv0tnIcMHB6GmrslFFxUwgpmbpaWJvUvZwEBjSIFX0ltjritOTqtTPQTbQyUiB2UTSdT5xR/iSNsaeF0GG3WEGvTnlVwV+VNU730n6piYqLJq8uT2aqCTGWNrJEJ38aSVXF8ojAtwkE4H33gcka2Lnx4NtxggTH7yXGlBwmFRFG9Qsv+DhA42/MkeIoJP0/4h20F+py0hYNKSHFOl5SpmN58QmGjxwm1gd6dFbuJIH3wXta3So1BDvWnq7MkHPz3vcuw5JBHqPVgyoOKRSTWbpZcnK2hzJ8G2DuUgZxs6XctBwzG0hA6i+soNNCZjnmm/3fTjNr6TmOG2EgRiQj2LkjX/CEb1TuYqIq31wqM2a+aYTQ12VcXLol5g1TF0h98RtryOVY5IxLVDfKNdJTsEZR9dmQW2orqbmv8weYAVrW9SrRPr8q+1KphRHdhJ4oYcoDY4xIbnD7TumjJ27ZSlPun6QLsE73z9fEiH79G61RhxB2cQJ1NxJr9EX5OdXcf2+MK5YTJ4BCeeHe/EXDsEV/PY</OrderData></DataTransfer><ReturnCode authenticate="true">000000</ReturnCode></body></ebicsKeyManagementResponse>', $version, $tocheck)))
        );

        $bank    = new Bank('myHostId', 'http://myurl.com', $version);
        $user    = new User('myPartId', 'myUserId');
        $keyRing = new KeyRing('');

        $keyRing->setUserCertificateEAndX(
            new UserCertificate(
                CertificatType::e(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new PrivateKey(FakeCrypt::RSA_PRIVATE_KEY),
                self::createMock(CertificateX509::class)
            ),
            new UserCertificate(
                CertificatType::x(),
                FakeCrypt::RSA_PUBLIC_KEY,
                new PrivateKey(FakeCrypt::RSA_PRIVATE_KEY),
                self::createMock(CertificateX509::class)
            )
        );
        $keyRing->setBankCertificate(
            new BankCertificate(
                CertificatType::x(),
                FakeCrypt::RSA_PUBLIC_KEY,
                self::createMock(CertificateX509::class)
            ),
            new BankCertificate(
                CertificatType::e(),
                FakeCrypt::RSA_PUBLIC_KEY,
                self::createMock(CertificateX509::class)
            ),
        );

        $sUT->__invoke($bank, $user, $keyRing);
    }
}
