<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\LoginActivity;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                LoginActivity::saveLoginActivity($user->id, 'login');
                $token = $user->createToken('user_token')->plainTextToken;
                $role = $user->getRoleNames()->first();
                $user['role'] = $role; 
                unset($user['roles']); 
                return ResponseTrait::success('Login successful', [
                    'user' => $user,
                    'token' => $token,
                ]);
            }
            return ResponseTrait::error('The provided credentials do not match our records.');
        } catch (\Throwable $e) {
            return ResponseTrait::error('An error occurred due to: ' . $e->getMessage());
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            // Create the user
            DB::beginTransaction();
            $user = User::register($request->all());
            $user->assignRole('client');
            $token = $user->createToken('user_token')->plainTextToken;
            DB::commit();
            return ResponseTrait::success('User registered successfully', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred due to: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            LoginActivity::saveLoginActivity($user->id, 'logout');
            $user->tokens()->delete();
            return ResponseTrait::success('User Logged Out Successfully');
        } catch (Exception $e) {
            return ResponseTrait::error('An error occurred due to: ' . $e->getMessage());
        }
    }
}
