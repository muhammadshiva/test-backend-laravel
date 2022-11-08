<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'category_id'
    ];

    public function moneyPlansCategories()
    {
        return $this->belongsTo(MoneyPlanCategory::class, 'category_id', 'id');
    }
}
