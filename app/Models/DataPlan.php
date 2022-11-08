<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPlan extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'price',
        'operator_card_id'
    ];

    public function operatorCards()
    {
        return $this->belongsTo(OperatorCard::class, 'operator_card_id', 'id');
    }
}
