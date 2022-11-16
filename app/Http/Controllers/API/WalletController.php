<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function fetch()
    {
        $user_id = Auth::user();
        $wallet = Wallet::firstWhere('user_id', $user_id->id);

        return ResponseFormatter::success([
            'balance' => $wallet->balance,
            'card_number' => $wallet->card_number,
        ]);
        // $id = $request->input('id');
        // $balance = $request->input('balance');
        // $pin = $request->input('pin');
        // $userId = $request->input('user_id');
        // $cardNumber = $request->input('card_number');
    }

    // public function updatePin(Request $request)
    // {


    //     $user_id = Auth::user();
    //     $wallet = Wallet::firstWhere('user_id', $user_id->id);
    //     $wallet->pin = $request->pin;
    //     $wallet->save();

    //     return ResponseFormatter::success($wallet, 'Pin Updated');
    // }

    public function updatePin(Request $request)
    {
        $pin = $request->previous_pin;
        $user = Auth::user();

        $checkPin = Wallet::where('user_id', $user->id)->where('pin', $pin)->first();

        if (!$checkPin) {
            return ResponseFormatter::error([
                'message' => 'Pin Salah',
            ]);
        }

        $checkPin->update([
            'pin' => $request->new_pin,
        ]);

        return ResponseFormatter::success([
            'message' => 'Pin Updated',
        ]);
    }
}
