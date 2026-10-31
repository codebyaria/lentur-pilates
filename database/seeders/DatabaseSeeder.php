<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Akun admin demo untuk panel Filament di /admin.
        $admin = User::factory()->create([
            'name' => 'Admin Lentur',
            'email' => 'admin@lentur.test',
            'password' => 'lentur-demo',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        $this->call([
            StudioSeeder::class,
            ScheduleSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
