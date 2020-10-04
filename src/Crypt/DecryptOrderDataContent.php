<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Crypt;

use Fezfez\Ebics\DOMDocument;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\OrderDataEncrypted;
use phpseclib\Crypt\AES;
use phpseclib\Crypt\RSA;
use RuntimeException;

use function Safe\gzuncompress;

use const OPENSSL_ZERO_PADDING;

class DecryptOrderDataContent
{
    public function __invoke(KeyRing $keyRing, OrderDataEncrypted $orderData): DOMDocument
    {
        $rsa = new RSA();
        $rsa->setPassword($keyRing->getPassword()->value());
        $rsa->loadKey($keyRing->getUserCertificateE()->getPrivateKey()->value());
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);

        $transactionKeyDecrypted = $rsa->decrypt($orderData->getTransactionKey());

        // aes-128-cbc encrypting format.
        $aes = new AES(AES::MODE_CBC);
        $aes->setKeyLength(128);
        $aes->setKey($transactionKeyDecrypted);
        // Force openssl_options.
        $aes->openssl_options = OPENSSL_ZERO_PADDING;

        $decrypted = $aes->decrypt($orderData->getOrderData());

        if (empty($decrypted)) {
            throw new RuntimeException('decrypt error');
        }

        return new DOMDocument(gzuncompress($decrypted));
    }
}
