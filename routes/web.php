<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;

// Route::get('/generate-ssl-keys', function () {
//     try {
//         $config = [
//             "digest_alg" => "sha256",
//             "private_key_bits" => 2048,
//             "private_key_type" => OPENSSL_KEYTYPE_RSA,
//         ];

//         $keyPair = openssl_pkey_new($config);

//         if (!$keyPair) {
//             throw new \Exception('Failed to generate key pair: ' . openssl_error_string());
//         }

//         openssl_pkey_export($keyPair, $privateKey);
//         $keyDetails = openssl_pkey_get_details($keyPair);
//         $publicKey = $keyDetails["key"];

//         return response()->json([
//             'success' => true,
//             'message' => 'Copy these BASE64 encoded keys to your .env file:',
//             'public_key_base64' => base64_encode($publicKey),
//             'private_key_base64' => base64_encode($privateKey),
//             'instructions' => 'Add these as single-line environment variables'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage()
//         ], 500);
//     }
// });

    Route::get('/create-admin', function () {
        try {
            // Prevent duplicate insert
            $existing = DB::table('staffs')->where('email', 'jane.doe@gmail.com')->first();
            if ($existing) {
                return 'Admin Jane Doe already exists.';
            }

            DB::table('staffs')->insert([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return '✅ Admin Jane Doe created successfully!';
        } catch (\Throwable $e) {
            return '❌ Error creating admin: ' . $e->getMessage();
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
