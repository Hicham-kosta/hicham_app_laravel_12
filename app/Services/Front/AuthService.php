<?php

namespace App\Services\Front;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * register a new user
     * 
     * @param array $data
     * @return App\Models\User
     */

    public function registerUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'] ?? 'Customer',
            'status' => 1
        ]);
        return $user;
    }

    /**
     * 
     * Attempt to login a user (checks status)
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        $user = User::where('email', $credentials['email'])->first();
        if ($user && (int)$user->status === 0) {
            return false;
        }

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            session()->regenerate();
            return true;
        }
        return false;
    }
}