<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;

Route::get('/generate-ssl-keys-storage', function () {
    try {
        // Use Laravel Storage which handles directory creation automatically
        \Illuminate\Support\Facades\Storage::makeDirectory('keys');

        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $keyPair = openssl_pkey_new($config);

        if (!$keyPair) {
            throw new \Exception('Failed to generate key pair: ' . openssl_error_string());
        }

        openssl_pkey_export($keyPair, $privateKey);
        $keyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $keyDetails["key"];

        // Save using Laravel Storage
        \Illuminate\Support\Facades\Storage::put('keys/private.pem', $privateKey);
        \Illuminate\Support\Facades\Storage::put('keys/public.pem', $publicKey);

        return response()->json([
            'success' => true,
            'message' => 'SSL keys generated using Laravel Storage!',
            'private_key_exists' => \Illuminate\Support\Facades\Storage::exists('keys/private.pem'),
            'public_key_exists' => \Illuminate\Support\Facades\Storage::exists('keys/public.pem')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
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
