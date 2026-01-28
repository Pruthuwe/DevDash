<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'mobile',
        'email',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'vat_no',
        'fax',
    ];
}
