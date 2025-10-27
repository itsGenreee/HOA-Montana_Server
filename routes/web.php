<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;

Route::get('/generate-compatible-ssl-keys', function () {
    try {
        // Create keys directory
        \Illuminate\Support\Facades\Storage::makeDirectory('keys');

        $keysPath = storage_path('app/keys');

        // Generate key pair using PHP OpenSSL (compatible with your DigitalSignature class)
        $config = [
            "digest_alg" => "sha256", // Same as your OPENSSL_ALGO_SHA256
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $keyPair = openssl_pkey_new($config);

        if (!$keyPair) {
            throw new \Exception('Failed to generate key pair: ' . openssl_error_string());
        }

        // Export private key in PEM format
        openssl_pkey_export($keyPair, $privateKey);

        // Get public key details
        $keyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $keyDetails["key"];

        // Save keys to files (exactly where your DigitalSignature class expects them)
        file_put_contents("{$keysPath}/private.pem", $privateKey);
        file_put_contents("{$keysPath}/public.pem", $publicKey);

        // Set proper permissions
        chmod("{$keysPath}/private.pem", 0600); // Read/write for owner only
        chmod("{$keysPath}/public.pem", 0644);  // Read for everyone

        // Test that the keys work with your DigitalSignature class
        $testData = "test_signature_data";
        $signature = \App\Helpers\DigitalSignature::sign($testData);
        $verification = \App\Helpers\DigitalSignature::verify($testData, $signature);

        return response()->json([
            'success' => true,
            'message' => 'SSL keys generated and tested successfully!',
            'keys_created' => [
                'private.pem' => file_exists("{$keysPath}/private.pem"),
                'public.pem' => file_exists("{$keysPath}/public.pem")
            ],
            'digital_signature_test' => [
                'test_data' => $testData,
                'signature' => $signature,
                'verification' => $verification ? '✅ PASSED' : '❌ FAILED'
            ],
            'key_paths' => [
                'private_key' => "{$keysPath}/private.pem",
                'public_key' => "{$keysPath}/public.pem"
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/download', [PageController::class, 'download'])->name('download');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/auth.php';
