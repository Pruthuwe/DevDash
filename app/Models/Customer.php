<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'mobile',
        'email',
        'password',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'vat_no',
        'fax',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}