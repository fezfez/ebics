<?php

declare(strict_types=1);

namespace Cube43\Ebics\X509;

use phpseclib\File\X509;

use function is_array;

/**
 * X509 extensions options normalizer.
 */
class X509ExtensionOptionsNormalizer
{
    /**
     * @see X509::setExtension()
     *
     * @param mixed|string|array $options
     *
     * @return array
     */
    public static function normalize($options): array
    {
        $value    = null;
        $critical = false;
        $replace  = true;

        if (! is_array($options)) {
            $value = $options;
        } else {
            if (! isset($options['value'])) {
                $value = $options;
            } else {
                $value = $options['value'];
                if (isset($options['critical'])) {
                    $critical = $options['critical'];
                }

                if (isset($options['replace'])) {
                    $replace = $options['replace'];
                }
            }
        }

        return [
            'value' => $value,
            'critical' => $critical,
            'replace' => $replace,
        ];
    }
}
