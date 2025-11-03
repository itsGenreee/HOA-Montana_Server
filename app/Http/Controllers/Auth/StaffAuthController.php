<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class StaffAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Simple manual authentication
        $staff = Staff::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $token = $staff->createToken('staff_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Staff login successful',
            'staff' => $staff->only(['id', 'first_name', 'last_name', 'email', 'role']),
            'token' => $token,
        ]);
    }

    public function loginAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Find staff and check credentials FIRST
        $staff = Staff::where('email', $request->email)->first();

        // Check if staff exists AND password is correct FIRST (security)
        if (!$staff || !Hash::check($request->password, $staff->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // THEN check if they're admin (after authentication)
        if ($staff->role !== 'admin') { // Changed from status to role
            return response()->json([
                'message' => 'Only administrators are allowed to login here'
            ], 403); // Added 403 Forbidden status
        }

        $token = $staff->createToken('staff_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'staff' => $staff->only(['id', 'first_name', 'last_name', 'email', 'role', 'status']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Staff logout successful'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $staff = $request->user();

        $staff->refresh();

        return response()->json([
            'staff' => $staff
        ]);
    }

    /**
     * Get all staff accounts
     */
    public function index(Request $request): JsonResponse
    {
        $staff = Staff::select(['id', 'first_name', 'last_name', 'email', 'role', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'staff' => $staff,
            'count' => $staff->count()
        ]);
    }

    /**
     * Create a new staff account
     */
    public function register(Request $request): JsonResponse
    {
        $currentStaff = auth('staff')->user();
        if ($currentStaff->role !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can create staff accounts'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:staffs'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,staff'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $staff = Staff::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Staff account created successfully',
            'staff' => $staff->only(['id', 'first_name', 'last_name', 'email', 'role', 'created_at'])
        ], 201);
    }

public function update(Request $request, $id): JsonResponse
{
    $staff = Staff::find($id);

    if (!$staff) {
        return response()->json([
            'message' => 'Staff account not found'
        ], 404);
    }

    $currentStaff = auth('staff')->user();

    // Staff can only update their own account, admins can update any account
    if ($currentStaff->role !== 'admin' && $currentStaff->id !== $staff->id) {
        return response()->json([
            'message' => 'You can only update your own account'
        ], 403);
    }

    // Staff cannot change their role, only admins can
    if ($request->has('role') && $currentStaff->role !== 'admin') {
        return response()->json([
            'message' => 'Only administrators can change roles'
        ], 403);
    }

    // Create validation rules based on what's actually in the request
    $rules = [
        'first_name' => ['sometimes', 'string', 'max:255'],
        'last_name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:staffs,email,' . $id],
        'role' => ['sometimes', 'string', 'in:admin,staff'],
    ];

    // Only add password validation if password is being changed
    if ($request->has('password')) {
        $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $updateData = [];
    if ($request->has('first_name')) {
        $updateData['first_name'] = $request->first_name;
    }
    if ($request->has('last_name')) {
        $updateData['last_name'] = $request->last_name;
    }
    if ($request->has('email')) {
        $updateData['email'] = $request->email;
    }
    if ($request->has('password')) {
        $updateData['password'] = Hash::make($request->password);
    }
    if ($request->has('role')) {
        $updateData['role'] = $request->role;
    }

    // Only update if there's actually data to update
    if (!empty($updateData)) {
        $staff->update($updateData);
    }

    return response()->json([
        'message' => 'Staff account updated successfully',
        'staff' => $staff->only(['id', 'first_name', 'last_name', 'email', 'role', 'updated_at'])
    ]);
}

    /**
     * Delete a staff account
     */
    public function destroy($id): JsonResponse
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'message' => 'Staff account not found'
            ], 404);
        }

        // Get the currently authenticated staff member
        $currentStaff = auth('staff')->user();

        // Check if current user is an admin
        if ($currentStaff->role !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can delete staff accounts'
            ], 403);
        }

        // Prevent deleting your own account
        if ($staff->id === $currentStaff->id) {
            return response()->json([
                'message' => 'Cannot delete your own account'
            ], 422);
        }

        // Prevent admins from deleting other admins (optional - add if you want)
        if ($staff->role === 'admin') {
            return response()->json([
                'message' => 'Cannot delete administrator accounts'
            ], 422);
        }

        $staff->delete();

        return response()->json([
            'message' => 'Staff account deleted successfully'
        ]);
    }
}
