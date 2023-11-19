<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Transaction extends Model
{
    use HasFactory, UUID;
    protected $fillable = [
        'user_id', //sender
        'user_bank_id', //receiver
        'amount',
        'transaction_code',
        'is_fraud_detected'
    ];

    public function User() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function UserBank() {
        return $this->belongsTo(UserBank::class, 'user_bank_id');
    }
}
