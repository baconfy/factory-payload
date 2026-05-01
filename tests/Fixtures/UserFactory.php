<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload\Tests\Fixtures;

use Baconfy\FactoryPayload\HasPayload;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    use HasPayload;

    protected $model = User::class;

    protected array $payloadAttributes = ['name', 'email'];

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