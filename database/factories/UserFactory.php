<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Role;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $genders = ['male', 'female'];
        $gender = $genders[array_rand($genders)];
        $role = Role::where('name', 'user')->first();
        return [
            'name' => $this->faker->name($gender),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->e164PhoneNumber,
            'phone_number_verified_at' => now(),
            'address' => $this->faker->streetAddress,
            'role_id' => $role->id,
            'avatar' => 'users/default.png',
        ];
    }
}
