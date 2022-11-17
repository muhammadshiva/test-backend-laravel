<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ResponseCustom;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            // Create User and Wallet
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'ktp' => $request->ktp,
                'password' => Hash::make($request->password),
            ]);

            $num = str_pad(mt_rand(1, 99999999), 16, '0', STR_PAD_LEFT);

            $wallet = Wallet::create([
                'balance' => 0,
                'pin' => $request->pin,
                'user_id' => $user->id,
                'card_number' => strval($num),
            ]);

            // Create Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user['token'] = $tokenResult;
            return ResponseCustom::success($user);
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseCustom::error(['message' => 'Invalid email or password']);
            }
            // $user = User::where('email', $request->email)->first();
            // $wallet = Wallet::where('user_id', $user->id)->first();

            $user = User::with('wallets')->firstWhere('email', $request->email);
            $wallet = Wallet::where('user_id', $user->id)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user['token'] = $tokenResult;
            return ResponseCustom::success(
                [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "username" => $user->username,
                    "verified" => $user->verified,
                    "email_verified_at" => $user->email_verified_at,
                    "profile_photo_path" => $user->profile_photo_path,
                    "ktp" => $user->ktp,
                    "created_at" => $user->created_at,
                    "updated_at" => $user->updated_at,
                    "balance" => $wallet->balance,
                    "card_number" => $wallet->card_number,
                    "pin" => $wallet->pin,
                    "token" => $user->token,
                    "token_type" => "bearer",
                ]
            );
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }

    public function isEmailExist(Request $request)
    {
        try {
            $check = User::where('email', $request->email)->exists();
            return ResponseCustom::success(['is_email_exist' => $check]);
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }


    public function getUser(Request $request)
    {
        try {
            return ResponseCustom::success($request->user());
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }

    public function getUserByUserName($username)
    {
        try {
            $users = User::where('username', $username)->get();
            return ResponseCustom::success($users);
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $data = $request->all();
            $user = Auth::user();
            $user->update($data);
            return ResponseCustom::success(['message' => 'Update success']);
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();
            return ResponseCustom::success(['message' => 'Logout successfully']);
        } catch (\Throwable $th) {
            return ResponseCustom::error($th->getMessage());
        }
    }
}
