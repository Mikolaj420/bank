<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id' => Role::where('name', Role::CLIENT)->value('id'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'pesel' => null,
            'password' => 'password',
            'remember_token' => Str::random(10),
        ];
    }

    /** Stan przypisujący dowolną rolę po jej nazwie (slug). */
    public function role(string $name): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('name', $name)->value('id'),
        ]);
    }
}
