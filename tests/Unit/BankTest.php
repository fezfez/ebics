<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Unit;

use Fezfez\Ebics\Bank;
use Fezfez\Ebics\Version;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Bank
 */
class BankTest extends TestCase
{
    public function testGetter(): void
    {
        $sUT = new Bank('test', 'test2', Version::v24());

        self::assertSame('test', $sUT->getHostId());
        self::assertSame('test2', $sUT->getUrl());
        self::assertSame(Version::v24()->value(), $sUT->getVersion()->value());
        self::assertTrue($sUT->isCertified());
    }
}
