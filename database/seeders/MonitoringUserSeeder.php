<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MonitoringUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'monitoring@geumcheonindo.com'],
            [
                'name' => 'Monitoring Dashboard',
                'password' => 'gciindo1!',
                'department_code' => 'MONITORING',
            ]
        );
    }
}
