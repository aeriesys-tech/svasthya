<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		$user = User::first();

		$appointments = [
			[				
				'appointee_name' => 'Manish',
				'designation' => 'SI',
				'phone_number' => '9876543210',
				'appointment_date' => '2025-10-20',
				'appointment_time' => '10:30:00',
				'place' => 'Jayanagara',
				'status' => 1,
			],
			[				
				'appointee_name' => 'Sachin',
				'designation' => 'PC',
				'phone_number' => '9988776655',
				'appointment_date' => '2025-10-21',
				'appointment_time' => '11:15:00',
				'place' => 'Koramangala',
				'status' => 0,
			],
		];

		foreach ($appointments as $appointment) {
			Appointment::create($appointment);
		}
	}
}
