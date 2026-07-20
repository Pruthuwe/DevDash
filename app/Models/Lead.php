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
        'lead_source', 'source_type', 'source_id', 'assigned_to', 'status', 'notes',
    ];

    protected $casts = [
        'next_followup_at' => 'datetime',
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

    /** The enquiry (LoanInquiry or ProductEnquiry) this lead was auto-converted from, if any. */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_type', 'source_id');
    }

    /** Status badge colour helper (used in Blade) */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Converted'       => 'bg-success-100 text-success-600',
            'Sales Done'      => 'bg-neutral-200 text-neutral-600',
            'Not Interested'  => 'bg-danger-100 text-danger-600',
            'Follow Up Later' => 'bg-warning-100 text-warning-600',
            'Need More Info'  => 'bg-lilac-100 text-lilac-600',
            'Interested'      => 'bg-info-100 text-info-600',
            default           => 'bg-neutral-100 text-neutral-600',
        };
    }

    /**
     * Leads with a scheduled next follow-up that's due today or overdue.
     * Reads straight off the cached leads.next_followup_at column kept in
     * sync by syncNextFollowupAt().
     */
    public function scopeFollowupDue($query)
    {
        return $query->whereIn('status', ['Need More Info', 'Follow Up Later'])
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now());
    }

    /** True when this lead has a scheduled follow-up that's due today or overdue. */
    public function getFollowupDueAttribute(): bool
    {
        return $this->next_followup_at !== null && $this->next_followup_at->isPast();
    }

    /**
     * Keeps leads.next_followup_at as a denormalized copy of the latest
     * follow-up's date — lets dashboard/list queries filter "who's due"
     * directly on the leads table instead of joining lead_followups every
     * time. The lead_followups row is still the source of truth; this is
     * just a cache, refreshed every time a follow-up is saved.
     */
    public function syncNextFollowupAt(): void
    {
        $this->forceFill([
            'next_followup_at' => $this->followups()->latest()->value('next_followup_at'),
        ])->save();
    }

    /**
     * Create or update the Lead row for a LoanInquiry/ProductEnquiry that
     * has just been marked "contacted" — these always count as a Web
     * Enquiry lead. Called from LoanInquiryController/ProductEnquiryController
     * ::updateStatus(). Idempotent: calling it again for the same enquiry
     * (e.g. contacted -> closed -> contacted again) updates the same Lead
     * row instead of creating duplicates, and — per the "latest contact
     * channel wins" rule — also takes over an existing lead with the same
     * phone number that came in through a different channel.
     */
    public static function fromContactedEnquiry(Model $enquiry): self
    {
        $lead = static::where('source_type', $enquiry::class)
            ->where('source_id', $enquiry->id)
            ->first()
            ?? static::where('phone', $enquiry->phone)->latest()->first()
            ?? new static();

        $product = $enquiry->relationLoaded('product') ? $enquiry->product : $enquiry->product()->first();

        $lead->fill([
            'name'                     => $enquiry->name,
            'phone'                    => $enquiry->phone,
            'city'                     => $enquiry->city ?? $lead->city,
            'lead_source'              => 'Web Enquiry',
            'source_type'              => $enquiry::class,
            'source_id'                => $enquiry->id,
            'vehicle_brand_id'         => $product?->subcategory_id ?? $lead->vehicle_brand_id,
            'vehicle_model_product_id' => $product?->id ?? $lead->vehicle_model_product_id,
            'vehicle_model_text'       => $product?->name ?? $lead->vehicle_model_text,
            'notes'                    => $enquiry->message ?? $lead->notes,
        ]);

        if (!$lead->exists) {
            $lead->customer_type = 'Individual';
            $lead->status        = 'Unassigned';
        }

        $lead->save();

        return $lead;
    }
}
