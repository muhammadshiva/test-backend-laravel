<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'amount',
        'category_id'
    ];

    public function moneyPlansCategories()
    {
        return $this->belongsTo(MoneyPlanCategory::class, 'category_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
