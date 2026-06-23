<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikeLoanPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'finance_company_id',
        'loan_amount',
        'interest_rate',
        'rmv',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function financeCompany()
    {
        return $this->belongsTo(FinanceCompany::class);
    }

    /**
     * Service charge is NOT manually editable — always 5% of the loan
     * amount, capped at Rs 25,000. Computed on the fly so it always
     * matches the current loan_amount.
     */
    public function getServiceChargeAttribute(): float
    {
        $raw = (float) $this->loan_amount * 0.05;
        return min($raw, 25000);
    }
}
