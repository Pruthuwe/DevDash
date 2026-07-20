<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
        ]);

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message'  => 'Registration successful',
            'token'    => $token,
            'customer' => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'email' => $customer->email,
            ]
        ]);
    }

    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    // Check customers table first
    $customer = Customer::where('email', $request->email)->first();

    if ($customer && Hash::check($request->password, $customer->password)) {
        if ($customer->status === 'inactive') {
            return response()->json([
                'message' => 'Your account has been deactivated'
            ], 403);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message'   => 'Login successful',
            'user_type' => 'customer',
            'token'     => $token,
            'customer'  => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'email' => $customer->email,
            ]
        ]);
    }

    // Check users table (admins/staff)
    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        if ($user->status === 'inactive') {
            return response()->json([
                'message' => 'Your account has been deactivated'
            ], 403);
        }

        return response()->json([
            'message'   => 'Login successful',
            'user_type' => 'admin',
            'redirect'  => route('dashboard'),
        ]);
    }

    // Not found in either table
    return response()->json([
        'message' => 'Invalid email or password'
    ], 401);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'customer' => $request->user()
        ]);
    }
}
