<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInquiry extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'phone',
        'city',
        'bike_price',
        'loan_amount',
        'bike_dp',
        'service_charge',
        'rmv',
        'minimum_dp',
        'interest_rate',
        'loan_term_months',
        'monthly_payment',
        'status',
    ];

    protected $casts = [
        'bike_price'       => 'decimal:2',
        'loan_amount'      => 'decimal:2',
        'bike_dp'          => 'decimal:2',
        'service_charge'   => 'decimal:2',
        'rmv'              => 'decimal:2',
        'minimum_dp'       => 'decimal:2',
        'interest_rate'    => 'decimal:2',
        'monthly_payment'  => 'decimal:2',
        'loan_term_months' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}