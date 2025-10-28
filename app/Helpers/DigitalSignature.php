<?php

namespace App\Helpers;

class DigitalSignature
{
    /**
     * Sign data with the private key.
     */
    public static function sign(string $data): string
    {
        $privateKeyContent = self::getPrivateKey();
        $privkey = openssl_pkey_get_private($privateKeyContent);

        if ($privkey === false) {
            throw new \Exception('Invalid private key: ' . openssl_error_string());
        }

        openssl_sign($data, $signature, $privkey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privkey);

        return base64_encode($signature);
    }

    /**
     * Verify data with the public key.
     */
    public static function verify(string $data, string $signature): bool
    {
        $publicKeyContent = self::getPublicKey();
        $pubkey = openssl_pkey_get_public($publicKeyContent);

        if ($pubkey === false) {
            throw new \Exception('Invalid public key: ' . openssl_error_string());
        }

        $result = openssl_verify($data, base64_decode($signature), $pubkey, OPENSSL_ALGO_SHA256);
        openssl_free_key($pubkey);

        switch ($result) {
            case 1:
                return true;  // Signature is valid
            case 0:
                return false; // Signature is invalid
            case -1:
                throw new \Exception('OpenSSL error: ' . openssl_error_string());
            default:
                throw new \Exception('Unexpected return value from openssl_verify');
        }
    }

    /**
     * Get private key from environment variable (base64 decoded)
     */
    private static function getPrivateKey(): string
    {
        $privateKeyBase64 = env('SSL_PRIVATE_KEY');

        if (!$privateKeyBase64) {
            throw new \Exception('SSL_PRIVATE_KEY environment variable not set.');
        }

        return base64_decode($privateKeyBase64);
    }

    /**
     * Get public key from environment variable (base64 decoded)
     */
    private static function getPublicKey(): string
    {
        $publicKeyBase64 = env('SSL_PUBLIC_KEY');

        if (!$publicKeyBase64) {
            throw new \Exception('SSL_PUBLIC_KEY environment variable not set.');
        }

        return base64_decode($publicKeyBase64);
    }
}
