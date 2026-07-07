<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'fixed_service_charge',
        'fixed_service_charge_amount',
    ];

    protected $casts = [
        'fixed_service_charge' => 'boolean',
        'fixed_service_charge_amount' => 'float',
    ];
}
