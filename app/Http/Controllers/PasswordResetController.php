<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetTokenRequest;
use App\Http\Requests\VerifyPasswordResetTokenRequest;
use App\Mail\SendOtpMail;
use App\Models\Client;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    use ResponseTrait;
    public function sendPasswordResetToken(SendPasswordResetTokenRequest $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->validated()['email'];
            $user = Client::where('email', $email)->first();
            $token = rand(100000, 999999);
            PasswordResetToken::saveToken($user->id, $token);
            DB::commit();
            Mail::to($email)->send(new SendOtpMail($token));
            return  ResponseTrait::success('Reset Token sent successfully', [
                'token' => $token,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred: ' . $e->getMessage(), null, 500);
        }
    }
    public function verifyPasswordResetToken(VerifyPasswordResetTokenRequest $request)
    {
        try {
            $email = $request->validated()['email'];
            $token = $request->validated()['token'];
            $user = Client::where('email', $email)->first();
            if ($user->resetToken->expires_at < now()) {
                return ResponseTrait::error('Token Expired');
            } else if ($token !== $user->resetToken->token) {
                return ResponseTrait::error('Reset Token Does Not Match');
            }
            return ResponseTrait::success('Reset Token verified successfully', [
                'user_id' => $user->id,
            ]);
        } catch (Exception $e) {
            return ResponseTrait::error('An error occurred: ' . $e->getMessage());
        }
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user_id = $request->validated()['user_id'];
            $user = User::where('id', $user_id)->first();
            User::changePassword($user, $request->validated());
            return ResponseTrait::success('Password Reset Successfully');
        } catch (Exception $e) {
            return ResponseTrait::error('An error occurred: ' . $e->getMessage());
        }
    }
}
