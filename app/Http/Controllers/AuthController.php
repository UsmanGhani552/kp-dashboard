<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Client;
use App\Models\LoginActivity;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
{
    try {
        $userRecord = \DB::table('users')->where('email', $request->email)->first();

        if (!$userRecord) {
            return ResponseTrait::error('The provided credentials do not match our records.');
        }

        // Detect model type using role
        $modelType = null;
        $modelRole = \DB::table('model_has_roles')
            ->where('model_id', $userRecord->id)
            ->where('model_type', 'App\\Models\\Client')
            ->value('role_id');

        if ($modelRole) {
            // This is a Client
            $client = Client::find($userRecord->id);
            if ($client && \Hash::check($request->password, $client->password)) {
                Auth::login($client);
                LoginActivity::saveLoginActivity($client->id, 'login');
                $token = $client->createToken('client_token')->plainTextToken;
                $role = $client->getRoleNames()->first();
                $client['role'] = $role;
                unset($client['roles']);
                $client->load('clientEmails');
                return ResponseTrait::success('Client login successful', [
                    'user' => $client,
                    'token' => $token,
                ]);
            }
        } else {
            // Assume it's a User (Admin)
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                LoginActivity::saveLoginActivity($user->id, 'login');
                $token = $user->createToken('user_token')->plainTextToken;
                $role = $user->getRoleNames()->first();
                $user['role'] = $role;
                unset($user['roles']);
                return ResponseTrait::success('User login successful', [
                    'user' => $user,
                    'token' => $token,
                ]);
            }
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
            $user = Client::register($request->all());
            $user->assignRole('client');
            $role = $user->getRoleNames()->first();
            $user['role'] = $role;
            unset($user['roles']);
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
