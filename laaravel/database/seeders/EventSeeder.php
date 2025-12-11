<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		$user = User::first();

		$events = [
			[
				'event_name' => 'Health Awareness Camp',
				'start_date' => '2025-11-10',
				'start_time' => '09:30:00',
				'venue' => 'Community Center, Bangalore',
				'session_by' => 'Dr. Madhu',
				'created_by' => $user->name,
				'updated_by' => $user->name,
			],
			[
				'event_name' => 'Wellness Talk',
				'start_date' => '2025-11-12',
				'start_time' => '11:00:00',
				'venue' => 'City Hospital Conference Hall',
				'session_by' => 'Dr. Madhu',
				'created_by' => $user->name,
				'updated_by' => $user->name,
			],
		];

		foreach ($events as $event) {
			Event::create($event);
		}
	}
}
