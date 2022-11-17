<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['nullable', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'ktp' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],
            ]);

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
                'pin' => '123123123',
                'user_id' => $user->id,
                'card_number' => strval($num),
            ]);

            // Store to user table
            // $user = User::where('email', $request->email)->first();

            // Create Token 
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
                'wallet' =>
                [
                    "balance" => $wallet->balance,
                    "card_number" => $wallet->card_number,
                    "pin" => $wallet->pin,
                ],
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {

        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid'
                ], 500);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                return response()->json([
                    'message' => 'Invalid'
                ], 500);
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user['token'] = $tokenResult;
            return response()->json($user, 200);
        } catch (Exception $error) {
            return response()->json($error->getMessage(), 500);
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success(
            $request->user(),
            'Data profile user berhasil diambil'
        );
    }

    public function updateProfile(Request $request)
    {

        $data = $request->all();

        $user = Auth::user();

        $user->update($data);

        return ResponseFormatter::success($user, 'Profile updated');
    }

    public function logout(Request $request)
    {

        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success(
            $token,
            'Token Revoked'
        );
    }

    public function isEmailExist(Request $request)
    {

        return ResponseFormatter::success(
            ['is_email_exist' => User::where('email', $request->email)->exists()]
        );
    }

    public function getUserByUserName($username)
    {
        try {
            $users = User::where('username', $username)->get();
            return ResponseFormatter::success(
                $users
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'User Not Found', 404);
        }
    }
}
