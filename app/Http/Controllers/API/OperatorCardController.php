<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OperatorCard;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class OperatorCardController extends Controller
{
    public function fetch(Request $request)
    {
        $operatorCard = OperatorCard::with('dataPlans')->paginate($request->limit);

        return ResponseFormatter::success(
            $operatorCard,
        );
    }
}
