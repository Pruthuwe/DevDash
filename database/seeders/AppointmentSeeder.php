<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = \App\Models\Customer::all();
        $services = \App\Models\Service::all();

        if ($customers->isEmpty() || $services->isEmpty()) {
            return;
        }

        \App\Models\Appointment::create([
            'customer_id' => $customers->first()->id,
            'service_id' => $services->first()->id,
            'appointment_date' => now()->addDays(2),
            'notes' => 'Sample appointment for testing',
            'status' => 'pending',
            'created_at' => now()->subDays(1),
        ]);

        \App\Models\Appointment::create([
            'customer_id' => $customers->last()->id,
            'service_id' => $services->first()->id,
            'appointment_date' => now()->addDays(3),
            'notes' => 'Another test appointment',
            'status' => 'approved',
            'created_at' => now()->subDays(2),
        ]);

        \App\Models\Appointment::create([
            'customer_id' => $customers->first()->id,
            'service_id' => $services->first()->id,
            'appointment_date' => now()->addDays(1),
            'notes' => 'Urgent service request',
            'status' => 'pending',
            'created_at' => now()->subHours(5),
        ]);
    }
}
