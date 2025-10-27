<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;


Route::get('/create-admin', function () {
    $user = \App\Models\Staff::create([
        'first_name' => 'Jhanra',
        'last_name' => 'Ordoviz',
        'role' => 'admin',
        'email' => 'jhanra.ordoviz@gmail.com',
        'password' => bcrypt('09300450062z1'), // Hash it here
        'created_at' => now()->setTime(19, 0, 0),
        'updated_at' => now()->setTime(19, 0, 0),
    ]);

    return "Admin user created with ID: " . $user->id;
});



Route::get('/seed-facility-fees', function () {
    try {
        // Clear existing fees first (optional)
        \App\Models\FacilityFee::truncate();

        // Tennis Court: flat rate only
        \App\Models\FacilityFee::create([
            'facility_id' => 1, // Tennis Court
            'type' => 'base',
            'fee' => 100,
            'discounted_fee' => 100,
            'start_time' => null,
            'end_time' => null,
            'name' => null,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        // Basketball Court: day and night shift
        \App\Models\FacilityFee::create([
            'facility_id' => 2, // Basketball Court for Day Shift
            'type' => 'shift',
            'name' => 'day',
            'fee' => 100,
            'discounted_fee' => 100,
            'start_time' => '06:00:00',
            'end_time' => '18:00:00',
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\FacilityFee::create([
            'facility_id' => 2, // Basketball Court for Night Shift
            'type' => 'shift',
            'name' => 'night',
            'fee' => 250,
            'discounted_fee' => 250,
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        // Event Place: blocks
        \App\Models\FacilityFee::create([
            'facility_id' => 3,
            'type' => 'base',
            'name' => 'Morning Event',
            'fee' => 12000,
            'discounted_fee' => 7000,
            'start_time' => '08:00:00',
            'end_time' => '13:00:00',
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\FacilityFee::create([
            'facility_id' => 3,
            'type' => 'base',
            'name' => 'Afternoon Event',
            'fee' => 12000,
            'discounted_fee' => 7000,
            'start_time' => '16:00:00',
            'end_time' => '21:00:00',
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facility fees seeded successfully with 7:00 PM timestamps!',
            'fees_count' => \App\Models\FacilityFee::count(),
            'fees' => \App\Models\FacilityFee::all()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/seed-amenities', function () {
    try {
        // Clear existing amenities first (optional)
        \App\Models\Amenity::truncate();

        // Create amenities using the model
        \App\Models\Amenity::create([
            'name' => 'Chair',
            'price' => 8.00,
            'max_quantity' => 30,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\Amenity::create([
            'name' => 'Videoke',
            'price' => 700.00,
            'max_quantity' => 1,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\Amenity::create([
            'name' => 'Projector Set',
            'price' => 1000.00,
            'max_quantity' => 1,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\Amenity::create([
            'name' => 'Brides Room',
            'price' => 2000.00,
            'max_quantity' => 1,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        \App\Models\Amenity::create([
            'name' => 'Island Garden for Pictorial',
            'price' => 150.00,
            'max_quantity' => 1,
            'created_at' => now()->setTime(19, 0, 0),
            'updated_at' => now()->setTime(19, 0, 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Amenities seeded successfully with 7:00 PM timestamps!',
            'amenities_count' => \App\Models\Amenity::count(),
            'amenities' => \App\Models\Amenity::all()
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
