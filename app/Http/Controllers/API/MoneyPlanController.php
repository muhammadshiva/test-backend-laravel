<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseCustom;
use App\Http\Controllers\Controller;
use App\Models\MoneyPlan;
use Illuminate\Http\Request;
use App\Models\MoneyPlanCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class MoneyPlanController extends Controller
{
    public function fetch(Request $request)
    {
        // $moneyPlan = MoneyPlan::with('moneyPlansCategories')->where('category_id', $request->id)->paginate($request->limit);
        // $plan = MoneyPlan::with('users')->firstwhere('user_id', $user->id)->paginate($request->limit);
        // $moneyPlanCategory = MoneyPlanCategory::where('id', $moneyPlan->category_id)->paginate($request->limit);

        // FIRST TRY
        // $user = Auth::user();
        // $moneyPlan = MoneyPlan::with('moneyPlansCategories')->where('user_id', $user->id)->paginate($request->limit);

        // SECOND TRY
        $user = Auth::user();
        $moneyPlan = DB::table('money_plans')
            ->select('money_plans.id', 'money_plans_categories.name', 'money_plans_categories.thumbnail', 'money_plans.name', 'money_plans.amount')
            ->join('users', 'money_plans.user_id', '=', 'users.id')
            ->join('money_plans_categories', 'money_plans.category_id', '=', 'money_plans_categories.id')
            ->where('users.id', '=', $user->id)
            ->get();

        return ResponseCustom::success(
            [
                'data' => $moneyPlan,
            ]
        );
    }

    public function getCategories(Request $request)
    {

        $planCategories = MoneyPlanCategory::paginate($request->limit);

        return ResponseCustom::success(
            $planCategories,
        );
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $categoryId = $request->category_id;
        $amount = $request->amount;
        $name = $request->name;

        $category = MoneyPlanCategory::find($categoryId);

        MoneyPlan::create([
            'user_id' => $user->id,
            'name' => $name,
            'amount' => $amount,
            'category_id' => $categoryId,
        ]);


        $checkPin = Wallet::where('user_id', $user->id)->first();

        $checkPin->update([
            'balance' => $checkPin->balance - $amount,
        ]);

        return ResponseCustom::success([
            'category_id' => $categoryId,
            'name' => $name,
            'amount' => $amount,
        ]);
    }

    public function update($id, Request $request)
    {
        $name = $request->name;
        $amount = $request->amount;

        MoneyPlan::where('id', $id)->update(
            [
                'name' => $name,
                'amount' => $amount,
            ],
        );

        return ResponseCustom::success(
            ['message' => 'Data Updated'],
        );
    }

    public function delete($id)
    {

        MoneyPlan::where('id', $id)->delete();

        return ResponseCustom::success(
            ['message' => 'Data Deleted'],
        );
    }
}
