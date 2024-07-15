<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepo implements AuthInterface
{
    // Attempt to log in with provided credentials and return token
    public function login($data): ?string
    {
        $credentials = $data->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    // Retrieve user by email
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    // Increment login attempts and lock account if attempts exceed threshold
    public function incrementLoginAttempts(User $user): void
    {
        $user->increment('login_attempts');
        if ($user->login_attempts >= 3) {
            $user->lock_until     = now()->addMinutes(5);
            $user->login_attempts = 0;
        }
        $user->save();
    }

    // Reset login attempts and unlock account
    public function resetLoginAttempts(User $user): void
    {
        $user->login_attempts = 0;
        $user->lock_until     = null;
        $user->save();
    }

    // Register a new user
    public function register($data): ?User
    {
        try {
            return User::create([
                'name'     => $data->name,
                'email'    => $data->email,
                'password' => $data->password,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
