<?php

namespace Database\Seeders;

use App\Models\Radio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RadioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		$user = User::first();

		$radios = [
			[
				'title' => 'Healthy Living Tips',
				'duration' => '15:00',
				'url' => 'https://example.com/healthy-living',
				'session_by' => 'Dr. Madhu',
				'user_id' => $user->id,
			],
			[
				'title' => 'Stress Management',
				'duration' => '20:00',
				'url' => 'https://example.com/stress-management',
				'session_by' => 'Dr. Madhu',
				'user_id' => $user->id,
			],
			[
				'title' => 'Nutrition and Fitness',
				'duration' => '18:30',
				'url' => 'https://example.com/nutrition-fitness',
				'session_by' => 'Dr. Madhu',
				'user_id' => $user->id,
			],
		];

		foreach ($radios as $radio) {
			Radio::create($radio);
		}
	}
}
