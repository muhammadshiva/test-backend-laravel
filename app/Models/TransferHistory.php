<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'transaction_code'
    ];

    public function senders()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receivers()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
