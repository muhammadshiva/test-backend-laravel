<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'action',
        'thumbnail'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_type_id', 'id');
    }
}
