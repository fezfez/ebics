<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\E2e\Command;

use Fezfez\Ebics\Bank;
use Fezfez\Ebics\Command\INICommand;
use Fezfez\Ebics\EbicsServerCaller;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\User;
use Fezfez\Ebics\UserCertificate;
use Fezfez\Ebics\Version;
use Fezfez\Ebics\X509\SilarhiX509Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use XmlValidator\XmlValidator;

use function print_r;

class INICommandTest extends TestCase
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
        //yield [Version::v30()];
    }

    /** @dataProvider provideVersion */
    public function testOk(Version $version): void
    {
        $tocheck = static function (string $response): void {
        };

        $versionToXmlResponse = [
            Version::v24()->value() => '<?xml version="1.0" encoding="UTF-8" standalone="no"?><ebicsKeyManagementResponse xmlns="http://www.ebics.org/H003" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Revision="1" Version="H003" xsi:schemaLocation="http://www.ebics.org/H003 http://www.ebics.org/H003/ebics_keymgmt_response.xsd"><header authenticate="true"><static/><mutable><ReturnCode>000000</ReturnCode><ReportText>[EBICS_OK] OK</ReportText></mutable></header><body><ReturnCode authenticate="true">000000</ReturnCode></body></ebicsKeyManagementResponse>',
            Version::v25()->value() => '<?xml version="1.0" encoding="UTF-8" standalone="no"?><ebicsKeyManagementResponse xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Revision="1" Version="H004" xsi:schemaLocation="urn:org:ebics:H004 ebics_keymgmt_response_H004.xsd"><header authenticate="true"><static/><mutable><OrderID>A07B</OrderID><ReturnCode>000000</ReturnCode><ReportText>[EBICS_OK] OK</ReportText></mutable></header><body><ReturnCode authenticate="true">000000</ReturnCode></body></ebicsKeyManagementResponse>',
        ];

        $sUT = new INICommand(
            new EbicsServerCaller(new MockHttpClient($this->getCallback($versionToXmlResponse[$version->value()], $version, $tocheck)))
        );

        $bank    = new Bank('myHostId', 'http://myurl.com', $version);
        $user    = new User('myPartId', 'myUserId');
        $keyRing = new KeyRing('');

        $keyRing = $sUT->__invoke($bank, $user, $keyRing, new SilarhiX509Generator());

        self::assertInstanceOf(UserCertificate::class, $keyRing->getUserCertificateA());
    }
}
