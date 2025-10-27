<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class DigitalSignature
{
    /**
     * Sign data with the private key.
     */
    public static function sign(string $data): string
    {
        // Use Laravel Storage instead of direct file path
        if (!Storage::exists('keys/private.pem')) {
            throw new \Exception('Private key not found in storage.');
        }

        $privateKeyContent = Storage::get('keys/private.pem');
        $privkey = openssl_pkey_get_private($privateKeyContent);

        if ($privkey === false) {
            throw new \Exception('Invalid private key.');
        }

        openssl_sign($data, $signature, $privkey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * Verify data with the public key.
     */
    public static function verify(string $data, string $signature): bool
    {
        if (!Storage::exists('keys/public.pem')) {
            throw new \Exception('Public key not found in storage.');
        }

        $publicKeyContent = Storage::get('keys/public.pem');
        $pubkey = openssl_pkey_get_public($publicKeyContent);

        if ($pubkey === false) {
            throw new \Exception('Invalid public key.');
        }

        $result = openssl_verify($data, base64_decode($signature), $pubkey, OPENSSL_ALGO_SHA256);
        return $result === 1;
    }
}
