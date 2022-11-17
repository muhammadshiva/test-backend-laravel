<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseCustom;
use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function fetch(Request $request)
    {
        $tip = Tip::paginate($request->limit);

        return ResponseCustom::success(
            $tip
            // 'data' => [
            //     // 'id' => $tip->id,
            //     'title' => $tip->title,
            //     'url' => $tip->url,
            //     'thumbnail' => $tip->thumbnail,
            // ],
        );
    }
}
