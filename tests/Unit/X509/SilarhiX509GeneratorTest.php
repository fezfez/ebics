<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Unit\X509;

use Fezfez\Ebics\X509\SilarhiX509Generator;
use PHPUnit\Framework\TestCase;

class SilarhiX509GeneratorTest extends TestCase
{
    public function testOk(): void
    {
        $sUT = new SilarhiX509Generator();

        self::assertMatchesRegularExpression('^[0-9]{74}$^', $sUT->getSerialNumber());
    }
}
