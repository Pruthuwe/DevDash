<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'city',
        'customer_type', 'company_name',
        'vehicle_brand_id', 'vehicle_model_product_id', 'vehicle_model_text',
        'budget_range', 'quantity_needed',
        'lead_source', 'assigned_to', 'status', 'priority', 'notes',
    ];

    /** Sales officer this lead is assigned to */
    public function officer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Vehicle Brand — a subcategory row in the shared `categories` table */
    public function brand()
    {
        return $this->belongsTo(Category::class, 'vehicle_brand_id');
    }

    /** Vehicle Model — links to an actual product when one was matched/selected */
    public function modelProduct()
    {
        return $this->belongsTo(Product::class, 'vehicle_model_product_id');
    }

    /** All follow-up records for this lead */
    public function followups()
    {
        return $this->hasMany(LeadFollowup::class);
    }

    /** Latest follow-up */
    public function latestFollowup()
    {
        return $this->hasOne(LeadFollowup::class)->latestOfMany();
    }

    /**
     * Display string for the model, whether or not it's linked to a real
     * product. Falls back to the free-text value entered for a model that
     * isn't in stock yet.
     */
    public function getVehicleModelDisplayAttribute(): ?string
    {
        return $this->modelProduct?->name ?? $this->vehicle_model_text;
    }

    /** Status badge colour helper (used in Blade) */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Converted'       => 'bg-success-100 text-success-600',
            'Interested'      => 'bg-info-100 text-info-600',
            'Closed'          => 'bg-neutral-200 text-neutral-600',
            'Not Interested'  => 'bg-danger-100 text-danger-600',
            'Follow Up Later' => 'bg-warning-100 text-warning-600',
            'Need More Info'  => 'bg-purple-100 text-purple-600',
            default           => 'bg-secondary-100 text-secondary-600',
        };
    }

    /** Priority badge colour helper */
    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'High'   => 'bg-danger-100 text-danger-600',
            'Low'    => 'bg-neutral-200 text-neutral-600',
            default  => 'bg-info-100 text-info-600',
        };
    }
}
