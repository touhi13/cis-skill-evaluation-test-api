<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Repositories\Auth\AuthInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    private AuthInterface $repository;

    // Injecting AuthInterface into the controller
    public function __construct(AuthInterface $repository)
    {
        $this->repository = $repository;
    }

    // Handle user login
    public function login(Request $request): JsonResponse
    {
        // Validate login request
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->repository->getUserByEmail($request->email);

        // Check if user account is locked
        if ($user->lock_until && $user->lock_until > now()) {
            return $this->ResponseError('Account locked. Try again later.', null, 'Account locked. Try again later.', 423);
        }

        // Generate JWT token
        $token = $this->repository->login($request);

        if (!$token) {
            // Check if user exists and password is correct
            if ($user) {
                // Increment login attempts if user exists
                $this->repository->incrementLoginAttempts($user);

                // Check if user account is locked
                if ($user->lock_until && $user->lock_until > now()) {
                    return $this->ResponseError('Account locked for 5 minutes. Try again later.', null, 'Account locked for 5 minutes. Try again later.', 423);
                }
            }
            return $this->ResponseError('Invalid credentials', null, 'Invalid credentials', 400);
        }

        // Reset login attempts if successful
        $this->repository->resetLoginAttempts($user);

        $data = [
            'token' => $token,
            'user'  => $user,
        ];

        return $this->ResponseSuccess($data, 'Login Successful', "Login Successful", 200);
    }

    // Handle user logout
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    // Handle user registration
    public function register(UserRegistrationRequest $request): JsonResponse
    {
        // Register user
        $user = $this->repository->register($request);

        if (!$user) {
            return $this->ResponseError('Registration Failed', 'Registration Failed', 400);
        }

        return $this->ResponseSuccess($user, 'Registration Successful', 200);
    }

    // generate new token
    public function generateToken(Request $request)
    {
        $user  = $request->user();
        $token = JWTAuth::fromUser($user);

        return $this->ResponseSuccess(['token' => $token, 'user' => $user], 'Successful', "Successful", 200);

    }
}
