<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		User::create([
			'name' => 'Dr.Madhu',
			'email' => 'madhu@gmail.com',
			'password' => Hash::make('password'),
            'phone_no' => '9874561230',
            'designation' => 'Consultant Psychologist',
            'sub_division' => 'Psychology',
            'contact_address' => 'RV college, bengaluru'
		]);
	}
}
