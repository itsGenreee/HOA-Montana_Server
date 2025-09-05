<?php

namespace App\Helpers;

class DigitalSignature
{
    /**
     * Sign data with the private key.
     */
    public static function sign(string $data): string
    {
        $privateKeyContent = file_get_contents(storage_path('app/keys/private.pem'));
        if (!$privateKeyContent) {
            throw new \Exception('Private key not found.');
        }

        $pkey = openssl_pkey_get_private($privateKeyContent);
        if ($pkey === false) {
            throw new \Exception('Invalid private key.');
        }

        openssl_sign($data, $signature, $pkey, OPENSSL_ALGO_SHA256);

        // No need to free key explicitly—PHP cleans up automatically
        return base64_encode($signature);
    }

    /**
     * Verify data with the public key.
     */
    public static function verify(string $data, string $signature): bool
    {
        $publicKeyContent = file_get_contents(storage_path('app/keys/public.pem'));
        if (!$publicKeyContent) {
            throw new \Exception('Public key not found.');
        }

        $pkey = openssl_pkey_get_public($publicKeyContent);
        if ($pkey === false) {
            throw new \Exception('Invalid public key.');
        }

        $result = openssl_verify($data, base64_decode($signature), $pkey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }
}
