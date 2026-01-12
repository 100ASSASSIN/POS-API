<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    /**
     * List users (exclude admin)
     */
    public function index(Request $request)
    {
        $users = User::whereIn('role', ['manager', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json($users);
    }

    /**
     * Create new user (manager / cashier)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role'          => 'required|in:manager,cashier',
            'status'        => 'required|boolean',
            'profile_image' => 'nullable|image|max:2048',
            'company_name'  => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gst_number'    => 'nullable|string|max:50',
            'website'       => 'nullable|url|max:255',
        ]);

        $base64Image = null;

        if ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $imageData = file_get_contents($imageFile->getRealPath());
            $base64Image = 'data:' . $imageFile->getMimeType() . ';base64,' . base64_encode($imageData);
        }

        $defaultRoute = match ($request->role) {
            'manager' => '/manager',
            'cashier' => '/cashier',
        };

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'role'               => $request->role,
            'status'             => $request->status,
            'profile_image'      => $base64Image,
            'default_role_route' => $defaultRoute,
            'company_name'       => $request->company_name,
            'location'           => $request->location,
            'phone'              => $request->phone,
            'gst_number'         => $request->gst_number,
            'website'            => $request->website,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201);
    }

    /**
     * Update user (manager / cashier)
     */
    public function update(Request $request, $id)
    {
        $user = User::whereIn('role', ['manager', 'cashier'])->findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'          => 'required|in:manager,cashier',
            'status'        => 'required|boolean',
            'password'      => 'nullable|min:6',
            'profile_image' => 'nullable|image|max:2048',
            'company_name'  => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gst_number'    => 'nullable|string|max:50',
            'website'       => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $imageData = file_get_contents($imageFile->getRealPath());
            $user->profile_image =
                'data:' . $imageFile->getMimeType() . ';base64,' . base64_encode($imageData);
        }

        $user->update([
            'name'               => $request->name,
            'email'              => $request->email,
            'role'               => $request->role,
            'status'             => $request->status,
            'password'           => $request->password
                ? Hash::make($request->password)
                : $user->password,
            'default_role_route' => match ($request->role) {
                'manager' => '/manager',
                'cashier' => '/cashier',
            },
            'company_name'       => $request->company_name,
            'location'           => $request->location,
            'phone'              => $request->phone,
            'gst_number'         => $request->gst_number,
            'website'            => $request->website,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
        ]);
    }

    /**
     * Delete user (manager / cashier only)
     */
    public function destroy($id)
    {
        $user = User::whereIn('role', ['manager', 'cashier'])->findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
