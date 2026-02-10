<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'appointment_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * Get the customer that owns the appointment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the service that owns the appointment.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}