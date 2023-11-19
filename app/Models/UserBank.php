<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class UserBank extends Model
{
    use HasFactory, UUID;
    protected $fillable = [
        'bank_id',
        'user_id',
        'account_name',
        'va_number',
        'rekening_number',
        'is_reported'
    ];

    public function Bank() {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function User() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
