<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Crypt;

use function array_unshift;
use function pack;
use function unpack;

class FilterBlockedChar
{
    /**
     * Filter hash of blocked characters.
     */
    public function __invoke(string $hash): string
    {
        $rsaSha256prefix  = [0x30, 0x31, 0x30, 0x0D, 0x06, 0x09, 0x60, 0x86, 0x48, 0x01, 0x65, 0x03, 0x04, 0x02, 0x01, 0x05, 0x00, 0x04, 0x20];
        $signedInfoDigest = unpack('C*', $hash);

        array_unshift($signedInfoDigest, ...$rsaSha256prefix);

        return pack('c*', ...$signedInfoDigest);
    }
}
