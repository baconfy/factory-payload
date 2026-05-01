<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload\Tests\Fixtures;

use Baconfy\FactoryPayload\Attributes\PayloadAttributes;
use Illuminate\Database\Eloquent\Factories\Factory;

#[PayloadAttributes('name', 'email')]
class AttributeUserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'hashed-secret',
            'remember_token' => 'remember-me',
        ];
    }
}
