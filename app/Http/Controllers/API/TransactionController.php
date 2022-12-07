<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseCustom;
use App\Http\Controllers\Controller;
use App\Models\DataPlan;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransferHistory;
use App\Models\User;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function callback(Request $request)
    {
        $result = $request->all();
        $orderId = $result['order_id'];
        $trx = Transaction::where('transaction_code', $orderId)->first();

        if ($trx) {
            $status = $result['transaction_status'];
            if (in_array($status, ['approve', 'settlement', 'capture'])) {
                $trx->update([
                    'status' => $status,
                ]);

                $userId = $trx->user_id;

                $wallet = Wallet::where('user_id', $userId)->first();

                if ($wallet) {
                    $wallet->update([
                        'balance' => $wallet->balance + $trx->amount,
                    ]);
                }
            }
        }
    }

    public function topUp(Request $request)
    {
        $amount = $request->amount;
        $pin = $request->pin;
        // $payment_method_code = $request->payment_method_code;

        $user = Auth::user();
        $checkPin = Wallet::where('user_id', $user->id)->where('pin', $pin)->first();

        if (!$checkPin) {
            return ResponseCustom::error([
                'message' => 'Pin Salah'
            ],);
        }


        // $paymentMethod = PaymentMethod::where('code', $payment_method_code)->first();
        $transactionType = TransactionType::where('code', 'top_up')->first();

        $codeTrx = Str::random(10);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_type_id' => $transactionType->id,
            // 'payment_method_id' => $paymentMethod->id,
            'amount' => $amount,
            'transaction_code' => $codeTrx,
            'status' => 'pending',
        ]);

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-lqL5M_ep_5WrzcjGBVJkbjo1';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = false;

        $response_midtrans = \Midtrans\Snap::createTransaction([
            'transaction_details' => [
                'order_id' => $codeTrx,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->email
            ]
        ]);

        return ResponseCustom::success(
            $response_midtrans,
        );
    }


    public function transfer(Request $request)
    {
        $amount = $request->amount;
        $pin = $request->pin;
        $send_to = $request->send_to;

        $sender = Auth::user();
        $checkPin = Wallet::where('user_id', $sender->id)->where('pin', $pin)->first();

        if (!$checkPin) {
            return 'Pin Salah';
        }

        $receiver = User::where('email', $send_to)->first();

        TransferHistory::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'transaction_code' => Str::random(10),
        ]);

        $walletSender = Wallet::where('user_id', $sender->id)->first();
        $walletSender->update([
            'balance' => $walletSender->balance - $amount
        ]);

        $walletReceiver = Wallet::where('user_id', $receiver->id)->first();
        $walletReceiver->update([
            'balance' => $walletReceiver->balance + $amount
        ]);

        //Get transactions

        $transactionTypeSender = TransactionType::where('code', 'transfer')->first();
        $transactionTypeReceive = TransactionType::where('code', 'receive')->first();

        $codeTrx = Str::random(10);

        Transaction::create([
            'user_id' => $sender->id,
            'transaction_type_id' => $transactionTypeSender->id,
            'amount' => $amount,
            'transaction_code' => $codeTrx,
            'status' => 'success',
        ]);

        Transaction::create([
            'user_id' => $receiver->id,
            'transaction_type_id' => $transactionTypeReceive->id,
            'amount' => $amount,
            'transaction_code' => $codeTrx,
            'status' => 'success',
        ]);

        return ResponseCustom::success([
            'user' => 'Transfer Sukses'
        ],);
    }

    public function getTransactions(Request $request)
    {
        $userId = Auth::user();
        $trx = Transaction::with('transactionType')->where('user_id', $userId->id)->paginate($request->limit);
        // $trxType = TransactionType::paginate($request->limit);

        return ResponseCustom::success(
            $trx
        );
    }

    public function getTransferHistories()
    {
        $senderId = Auth::user();

        // $trx = TransferHistory::with('receivers')->where('sender_id', $senderId->id)->paginate($request->limit);
        //     $receiver = $item->receivers;
        //     return [
        //         'id' => $receiver->id,
        //         'name' => $receiver->name,
        //         'username' => $receiver->username,
        //         'verified' => 1,
        //         'profile_picture' => $receiver->profile_photo_path,
        //     ];
        // });

        $trx = TransferHistory::with('receivers')->where('sender_id', $senderId->id)
            ->map(function ($item) {
                $receiver = $item->receivers;
                return [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'username' => $receiver->username,
                    'verified' => 1,
                    'profile_picture' => $receiver->profile_photo_path,
                ];
            });

        return ResponseCustom::success(
            $trx,
        );
    }

    public function dataPlans(Request $request)
    {
        $dataPlanId = $request->data_plan_id;
        $phoneNumber = $request->phone_number;
        $pin = $request->pin;

        $user = Auth::user();
        $checkPin = Wallet::where('user_id', $user->id)->where('pin', $pin)->first();

        if (!$checkPin) {
            return ResponseCustom::error(
                [
                    'message' => 'Pin Salah'
                ]
            );
        }

        $dataPlan = DataPlan::find($dataPlanId);

        $checkPin->update([
            'balance' => $checkPin->balance - $dataPlan->price,
        ]);

        // Get transactions
        $user = Auth::user();

        $transactionType = TransactionType::where('code', 'internet')->first();

        $codeTrx = Str::random(10);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_type_id' => $transactionType->id,
            'amount' => $dataPlan->price,
            'transaction_code' => $codeTrx,
            'status' => 'success',
        ]);


        return ResponseCustom::success([
            'message' => 'Buy Data Plan Success',
        ]);
    }
}
