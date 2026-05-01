<?php

declare(strict_types=1);

use Baconfy\FactoryPayload\HasPayloadAttributes;
use Baconfy\FactoryPayload\Tests\Fixtures\User;
use Baconfy\FactoryPayload\Tests\Fixtures\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

it('returns empty array when payloadAttributes is not declared', function (): void {
    $factory = new class extends Factory {
        protected $model = User::class;

        public function definition(): array
        {
            return ['name' => 'Test'];
        }

        use HasPayloadAttributes;
    };

    expect($factory->payload())->toBe([]);
});

it('returns only overrides when payloadAttributes is not declared', function (): void {
    $factory = new class extends Factory {
        protected $model = User::class;

        public function definition(): array
        {
            return ['name' => 'Test'];
        }

        use HasPayloadAttributes;
    };

    expect($factory->payload(['foo' => 'bar']))->toBe(['foo' => 'bar']);
});

it('filters raw attributes by whitelist', function (): void {
    $payload = UserFactory::new()->payload();

    expect($payload)->toHaveKey('name')
        ->and($payload)->toHaveKey('email')
        ->and($payload)->not->toHaveKey('password')
        ->and($payload)->not->toHaveKey('remember_token');
});

it('merges overrides with filtered attributes', function (): void {
    $payload = UserFactory::new()->payload(['name' => 'Renato Dehnhardt']);

    expect($payload['name'])->toBe('Renato Dehnhardt');
});

it('allows overrides to bypass the whitelist', function (): void {
    $payload = UserFactory::new()->payload(['password_confirmation' => 'secret']);

    expect($payload)->toHaveKey('name')
        ->and($payload['password_confirmation'])->toBe('secret')
        ->and($payload)->not->toHaveKey('password')
        ->and($payload)->not->toHaveKey('remember_token');
});

it('returns a single array even when count is set', function (): void {
    $payload = UserFactory::new()->count(3)->payload();

    expect($payload)->toHaveKey('name')
        ->and($payload['name'])->toBeString();
});

it('works with state modifiers', function (): void {
    $payload = UserFactory::new()->state(['name' => 'Renato'])->payload();

    expect($payload['name'])->toBe('Renato');
});