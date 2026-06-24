<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    protected $fillable = [
        'lead_id', 'done_by', 'followup_at', 'method', 'feedback', 'status',
    ];

    protected $casts = [
        'followup_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

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
}
