<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseCustom;
use App\Http\Controllers\Controller;
use App\Models\MoneyPlan;
use Illuminate\Http\Request;
use App\Models\MoneyPlanCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

class MoneyPlanController extends Controller
{
    public function fetch(Request $request)
    {
        // $moneyPlan = MoneyPlan::with('moneyPlansCategories')->where('category_id', $request->id)->paginate($request->limit);

        $user = Auth::user();
        $plan = MoneyPlanCategory::with('moneyPlans')->where('user_id', $user->id)->paginate($request->limit);

        return ResponseCustom::success(
            $plan,
        );
    }

    public function create(Request $request)
    {

        $categoryId = $request->category_id;
        $amount = $request->amount;
        $name = $request->name;

        $category = MoneyPlanCategory::find($categoryId);

        $moneyPlan = MoneyPlan::create([
            'name' => $name,
            'amount' => $amount,
            'category_id' => $categoryId,
        ]);

        $user = Auth::user();
        $checkPin = Wallet::where('user_id', $user->id)->first();

        $checkPin->update([
            'balance' => $checkPin->balance - $amount,
        ]);

        return ResponseCustom::success([
            'message' => $moneyPlan,
        ]);
    }
}
