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
        'service_charge',
        'service_charge_percent',
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
     * Service charge is now a real, stored column — set at save time by the
     * Loan Calculator (either the finance company's fixed amount, or
     * service_charge_percent x loan_amount). No more hardcoded 5% / Rs
     * 25,000 cap here; that logic has been removed in favour of whatever
     * was actually configured per finance company / entered on save.
     */
}
