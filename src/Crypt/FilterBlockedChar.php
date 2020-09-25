<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Crypt;

use function array_merge;
use function array_values;
use function call_user_func_array;
use function count;
use function unpack;

class FilterBlockedChar
{
    /**
     * Filter hash of blocked characters.
     */
    public function __invoke(string $hash): string
    {
        $rsaSha256prefix  = [0x30, 0x31, 0x30, 0x0D, 0x06, 0x09, 0x60, 0x86, 0x48, 0x01, 0x65, 0x03, 0x04, 0x02, 0x01, 0x05, 0x00, 0x04, 0x20];
        $signedInfoDigest = array_values(unpack('C*', $hash));
        $digestToSign     = $this->systemArrayCopy($rsaSha256prefix, [], 0, count($rsaSha256prefix));
        $digestToSign     = $this->systemArrayCopy($signedInfoDigest, $digestToSign, count($rsaSha256prefix), count($signedInfoDigest));

        return $this->arrayToBin($digestToSign);
    }

    /**
     * System.arrayCopy java function interpretation.
     */
    private function systemArrayCopy(array $a, array $b, int $d, int $length): array
    {
        $c = 0;

        for ($i = 0; $i < $length; ++$i) {
            $b[$i + $d] = $a[$i + $c];
        }

        return $b;
    }

    /**
     * Pack array of bytes to one bytes-string.
     *
     * @param  array<int, int> $bytes
     *
     * @return string (bytes)
     */
    private function arrayToBin(array $bytes): string
    {
        return call_user_func_array('pack', array_merge(['c*'], $bytes));
    }
}
