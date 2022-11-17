<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OperatorCard;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class OperatorCardController extends Controller
{
    public function fetch()
    {
        $operator = OperatorCard::all();

        return ResponseFormatter::success([
            $operator,
        ]);
    }
}
