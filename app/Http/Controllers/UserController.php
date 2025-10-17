<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get all unverified users
     */
    public function getUnverifiedUsers(): JsonResponse
    {
        $users = User::where('status', User::STATUS_UNVERIFIED)
                    ->select('id', 'first_name', 'last_name', 'email', 'status', 'created_at')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count()
        ]);
    }

    /**
     * Get all verified users
     */
    public function getVerifiedUsers(): JsonResponse
    {
        $users = User::where('status', User::STATUS_VERIFIED)
                    ->select('id', 'first_name', 'last_name', 'email', 'status', 'created_at')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count()
        ]);
    }

    /**
     * Get all users with pagination (optional)
     */
    public function getAllUsers(Request $request): JsonResponse
    {
        $status = $request->get('status'); // Optional status filter
        $query = User::query();

        if ($status !== null) {
            $query->where('status', $status);
        }

        $users = $query->select('id', 'first_name', 'last_name', 'email', 'status', 'created_at')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage(),
                'total_users' => $users->total(),
                'per_page' => $users->perPage()
            ]
        ]);
    }

    /**
     * Update user status (verify a user)
     */
    public function verifyUser(Request $request, $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->status = User::STATUS_VERIFIED;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User verified successfully',
            'data' => $user->only(['id', 'first_name', 'last_name', 'email', 'status'])
        ]);
    }

    /**
     * Update user status (reject verification)
     */
    public function rejectUser(Request $request, $userId): JsonResponse
    {
        // You might want to add a reason or soft delete instead
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Option 1: Keep as unverified
        // Option 2: Soft delete or mark as rejected
        $user->status = User::STATUS_UNVERIFIED;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User verification rejected',
            'data' => $user->only(['id', 'first_name', 'last_name', 'email', 'status'])
        ]);
    }
}
