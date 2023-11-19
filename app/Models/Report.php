<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Report extends Model
{
    use HasFactory, UUID;
    protected $fillable = [
        'bank_id',
        'suspect_account_name',
        'suspect_account_number',
        'suspect_phone',
        'platform_id',
        'product_category_id',
        'chronology',
        'loss_amount',
        'user_id',
        'reporter_name',
        'identity',
        'identity_number',
        'reporter_phone'
    ];
}
