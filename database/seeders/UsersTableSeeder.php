<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin2@admin.com',
            'role_id' => Role::where('name', 'admin')->firstOrFail(),
            'phone_number' => '998908081239'
        ]);

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'role_id' => Role::where('name', 'administrator')->firstOrFail(),
        ]);

        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'role_id' => Role::where('name', 'user')->firstOrFail(),
        ]);

        // User::factory()->count(100)->create();

    }
}
