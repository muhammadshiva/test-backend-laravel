<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function fetch(Request $request)
    {
        $tip = Tip::all();

        return ResponseFormatter::success([
            'tip' => $tip,
        ]);
    }
}
