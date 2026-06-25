<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductEnquiry extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'phone',
        'city',
        'message',
        'status',
    ];

    /**
     * The bike this enquiry was made about.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}