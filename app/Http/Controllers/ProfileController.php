<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the "My Profile" page — available to any logged-in user
     * (admin or regular staff), so everyone can change their own password
     * without needing to go through Settings → Users, which is admin-only.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Update the current user's own password. Requires the current
     * password to be confirmed first, for basic account-takeover
     * protection (e.g. someone at an unlocked, logged-in workstation).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Your password has been updated successfully.');
    }
}
