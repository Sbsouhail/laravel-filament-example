<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {

        $admins = [
            [
                'first_name' => 'Souhail',
                'last_name' => 'SBOUI',
                'gender' => Gender::MALE->value,
                'date_of_birth' => '1998-04-03',
                'phone' => '+21628823442',
                'email' => 'sbsouhail@gmail.com',
                'password' => 'ChangedForExample',
                'is_admin' => true,
            ],
        ];

        foreach ($admins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );
        }
    }
}
