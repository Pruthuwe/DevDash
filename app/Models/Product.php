<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'category_id',
        'subcategory_id',
        'brand',
        'unit',
        'barcode',
        'price',
        'sale_price',
        'loan_amount',   
        'rmv',           
        'service_charge', 
        'cost_price',
        'quantity',
        'low_stock_alert',
        'tax',
        'tax_type',
        'short_description',
        'full_description',
        'main_image',
        'gallery_images',
        'tags',
        'notes',
        'status',
        'is_featured',
        'views',
        'engine_spec',
        'highlights',
        'rating',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_featured'    => 'boolean',
        'highlights'     => 'array',
        'rating'         => 'decimal:1',
        'price'          => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'loan_amount'    => 'decimal:2',  // ✅ NEW
        'rmv'            => 'decimal:2',  // ✅ NEW
        'service_charge' => 'decimal:2',  // ✅ NEW
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->price > 0) {
            return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
        }
        return 0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    // ✅ NEW — Loan calculator computed fields
    public function getLoanCalcAttribute()
    {
        $sellingPrice   = floatval($this->sale_price ?? $this->price);
        $loanAmount     = floatval($this->loan_amount ?? 0);
        $rmv            = floatval($this->rmv ?? 10160);

        $bikeDP         = $sellingPrice - $loanAmount;
        $serviceCharge  = min($loanAmount * 0.05, 25000);
        $minimumDP      = $bikeDP + $serviceCharge + $rmv;

        return [
            'selling_price'  => $sellingPrice,
            'loan_amount'    => $loanAmount,
            'bike_dp'        => $bikeDP,
            'service_charge' => $serviceCharge,
            'rmv'            => $rmv,
            'minimum_dp'     => $minimumDP,
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
}