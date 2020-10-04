<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Unit;

use Fezfez\Ebics\CertificateX509;
use Fezfez\Ebics\Models\Certificate;
use Fezfez\Ebics\Tests\E2e\FakeCrypt;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass Certificate
 */
class CertificateX509Test extends TestCase
{
    public function testGetter(): void
    {
        $sUT = new CertificateX509(FakeCrypt::X509_PUBLIC);

        self::assertSame(FakeCrypt::X509_PUBLIC, $sUT->value());
        self::assertSame('test.com', $sUT->getInsurerName());
        self::assertSame('413815081242434295596379023688818986270342478861', $sUT->getSerialNumber());
        self::assertSame('C0 3C FB A7 A5 47 42 9F
80 65 CC 10 7E 7D E9 D3
00 6C 9D 40 11 39 F6 06
07 FD 27 AC 09 5F 40 26', $sUT->digest());
    }

    public function testEmptyFail(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('x509 key is empty');

        new CertificateX509('');
    }

    public function testWrongKey(): void
    {
        $sUT = new CertificateX509(FakeCrypt::X509_WRONG_PUBLIC);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('unable to get id-at-commonName from certificate');

        $sUT->getInsurerName();
    }
}
