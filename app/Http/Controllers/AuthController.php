<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\SendOtpMail;
use App\Models\Client;
use App\Models\LoginActivity;
use App\Models\User;
use App\Traits\ResponseTrait;
use App\Traits\SendMailTrait;
use Exception;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use SendMailTrait;
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
                    $token = $client->createToken('client_token', expiresAt: now()->addDay())->plainTextToken;
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
                    if ($user->hasRole('admin')) {
                        // 2FA
                        $this->sendTwoFactorCode($user);
                        return ResponseTrait::success('Two-factor code sent to your email.', [
                            'user_id' => $user->id,
                            'role' => $user->getRoleNames()->first(),
                        ]);
                    } else {
                        return $this->loginResponse($user);
                    }
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
            // dd($user);
            $this->sendRegistrationDetailsToClient($user);
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

    public function sendTwoFactorCode(User $user)
    {
        $twoFactorCode = rand(100000, 999999);
        $user->update(['two_factor_code' => $twoFactorCode]);
        Mail::to('peter@koderspedia.com')->send(new SendOtpMail($twoFactorCode));
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'two_factor_code' => 'required|integer|digits:6',
        ]);

        $user = User::find($request->user_id);
        if ($user->two_factor_code == $request->two_factor_code) {
            $user->update(['two_factor_code' => null]); // Clear the code
            return $this->loginResponse($user);
        }

        return ResponseTrait::error('Invalid two-factor code.');
    }

    public function loginResponse($user) {
        LoginActivity::saveLoginActivity($user->id, 'login');
            $token = $user->createToken('user_token', expiresAt: now()->addDay())->plainTextToken;
            $role = $user->getRoleNames()->first();
            $user['role'] = $role;
            unset($user['roles']);
            return ResponseTrait::success('User login successful', [
                'user' => $user,
                'token' => $token,
            ]);
    }

    public function getUser()
    {
        try {
            $user = Auth::user();
            $role = $user->getRoleNames()->first();
            $user['role'] = $role;
            unset($user['roles']);
            return ResponseTrait::success('User login successful', [
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred due to: ' . $th->getMessage());
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

    public function uploadToBase64(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,svg,gif|max:5120'
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $base64 = base64_encode(file_get_contents($file->getRealPath()));

        return response()->json([
            'filename' => $filename,
            'base64'   => $base64
        ]);
    }

    public function base64ToFile($base64String, $extension = 'png')
    {
        try {
            $fileData = base64_decode($base64String);

            // Create a unique filename
            $filename = Str::uuid() . '.' . $extension;

            // Store temporarily in storage/app/tmp
            $tempPath = storage_path('app/tmp/' . $filename);
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            file_put_contents($tempPath, $fileData);

            // Return Laravel File object
            return new File($tempPath);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
