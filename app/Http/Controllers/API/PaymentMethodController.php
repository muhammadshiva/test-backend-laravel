<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function all()
    {
        $paymentMethod = PaymentMethod::all();

        return ResponseFormatter::success([
            'payment_methods' => $paymentMethod,
        ]);
    }
}
