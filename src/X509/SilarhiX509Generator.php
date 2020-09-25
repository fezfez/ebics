<?php

declare(strict_types=1);

namespace Fezfez\Ebics\X509;

/**
 * Legacy X509 certificate generator @see X509Generator.
 */
class SilarhiX509Generator extends BaseX509Generator
{
    protected function getCertificateOptions(array $options = []): array
    {
        return [
            'subject' => [
                'domain' => 'silarhi.fr',
                'DN' => [
                    'id-at-countryName' => 'FR',
                    'id-at-stateOrProvinceName' => 'Occitanie',
                    'id-at-localityName' => 'Toulouse',
                    'id-at-organizationName' => 'SILARHI',
                    'id-at-commonName' => 'silarhi.fr',
                ],
            ],
            'extensions' => [
                'id-ce-subjectAltName' => [
                    'value' => [
                        ['dNSName' => '*.silarhi.fr'],
                    ],
                ],
                'id-ce-basicConstraints' => [
                    'value' => ['CA' => false],
                ],
                'id-ce-keyUsage' => [
                    'value' => ['keyEncipherment', 'digitalSignature', 'nonRepudiation'],
                    'critical' => true,
                ],
                'id-ce-extKeyUsage' => [
                    'value' => ['id-kp-serverAuth', 'id-kp-clientAuth'],
                ],
            ],
        ];
    }
}
