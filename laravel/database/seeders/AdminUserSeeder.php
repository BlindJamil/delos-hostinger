<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'blindjamil@mhaplus.com'],
            [
                'name' => 'Blind Jamil',
                'password' => 'DELOS-Admin-BlindJamil@MHA',
                'is_super' => true,
            ]
        );
    }
}
