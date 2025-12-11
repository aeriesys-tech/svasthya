<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		$user = User::first();

		Availability::create([
			'user_id' => $user->id,
			'name' => 'Dr. Madhu',
			'designation' => 'Consultant Physician',
			'venue' => 'City Hospital, Bangalore',
			'time_slot' => '10:00 AM - 2:00 PM',
		]);
	}
}
