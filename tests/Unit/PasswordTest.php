<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Unit;

use Fezfez\Ebics\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testOk(): void
    {
        self::assertSame('test', (new Password('test'))->value());
    }
}
