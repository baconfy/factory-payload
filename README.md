# Factory Payload

Generate clean HTTP request payloads from Laravel Eloquent factories.

[![Tests](https://github.com/baconfy/factory-payload/actions/workflows/tests.yml/badge.svg)](https://github.com/baconfy/factory-payload/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)
[![License](https://img.shields.io/packagist/l/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)
[![Total Downloads](https://img.shields.io/packagist/dt/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)
[![PHP Version](https://img.shields.io/packagist/php-v/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)

## Why?

When testing HTTP endpoints, you need request payloads that match the shape your endpoint expects, not the shape your model stores.

### The simple case

Without this package:

```php
$response = $this->postJson(route('posts.store'), [
    'title' => fake()->sentence(),
    'body' => fake()->paragraph(),
]);
```

With this package:

```php
$response = $this->postJson(route('posts.store'), Post::factory()->payload());
```

One line. Self-documenting. Reusable across every test that hits this endpoint.

### When you need overrides

Sometimes you need to control specific fields, for example to test validation or assert against known values:

```php
$response = $this->postJson(
    route('posts.store'),
    Post::factory()->payload(['title' => ''])
);

$response->assertJsonValidationErrors(['title']);
```

Overrides always pass through, even if the field isn't part of the model's stored attributes (useful for things like `password_confirmation`).

## Installation

```bash
composer require --dev baconfy/factory-payload
```

Requires PHP 8.3+ and Laravel 11, 12 or 13.

## Usage

This package supports three equivalent ways to declare which attributes belong in the HTTP payload. Choose the style that best matches your project.

### Using `#[PayloadAttributes]`

Declare the payload attributes directly on the factory class:

```php
namespace Database\Factories;

use App\Models\Post;
use Baconfy\FactoryPayload\Attributes\PayloadAttributes;
use Illuminate\Database\Eloquent\Factories\Factory;

#[PayloadAttributes('title', 'body')]
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'user_id' => User::factory(),
            'published_at' => now(),
        ];
    }
}
```

### Using `HasPayloadAttributes`

Add the `HasPayloadAttributes` trait and define `$payloadAttributes` on your factory:

```php
namespace Database\Factories;

use App\Models\Post;
use Baconfy\FactoryPayload\HasPayloadAttributes;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    use HasPayloadAttributes;

    protected $model = Post::class;

    /**
     * @var array<int, string>
     */
    protected array $payloadAttributes = ['title', 'body'];

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'user_id' => User::factory(),
            'published_at' => now(),
        ];
    }
}
```

Both examples produce the same payload in your tests:

```php
$payload = Post::factory()->payload();
// ['title' => 'Lorem ipsum...', 'body' => 'Dolor sit amet...']
```

Notice how `user_id` and `published_at` are automatically excluded because they belong to persistence, not to the HTTP request.

### Using a DTO class

If your project already declares request shapes through Data Transfer Objects (DTOs), you can resolve the payload shape directly from the DTO class. Useful when you have multiple endpoints (create, update, etc.) sharing the same model.

```php
namespace App\Data;

class PostCreateData
{
    public static function keys(): array
    {
        return ['title', 'body'];
    }
}
```

```php
$payload = Post::factory()->payload(PostCreateData::class);
// ['title' => 'Lorem ipsum...', 'body' => 'Dolor sit amet...']
```

The DTO resolution follows these rules:

1. If the class has a static `keys(): array` method, its return value is used as the whitelist (compatible with [`spatie/laravel-data`](https://github.com/spatie/laravel-data) and similar libraries).
2. Otherwise, falls back to the class's public properties via Reflection:

```php
class PostUpdateData
{
    public ?string $title = null;
    public ?string $body = null;
}

$payload = Post::factory()->payload(PostUpdateData::class);
// ['title' => '...', 'body' => '...']
```

If the class doesn't exist, an `InvalidArgumentException` is thrown with a clear message.

> **Note:** When passing a DTO class, overrides are not supported in the same call. If you need both, use the array form: `payload(['title' => 'custom'])`.

### With Pest datasets

Test multiple invalid scenarios in one go:

```php
it('rejects invalid post payloads', function (array $overrides, string $errorField): void {
    $response = $this->postJson(
        route('posts.store'),
        Post::factory()->payload($overrides)
    );

    $response->assertStatus(422)->assertJsonValidationErrors([$errorField]);
})->with([
    'missing title' => [['title' => ''], 'title'],
    'missing body' => [['body' => ''], 'body'],
    'title too long' => [['title' => str_repeat('a', 300)], 'title'],
]);
```

## Behavior

The behavior below applies to all three ways of declaring payload attributes:

| Scenario | Result |
|----------|--------|
| No payload attributes declared | Returns only the overrides |
| `#[PayloadAttributes]` or `$payloadAttributes` declared | Filters `raw()` by whitelist, then merges overrides |
| Override key exists in whitelist | Override wins |
| Override key not in whitelist | Override still passes through |
| Factory has `count()` set | `payload()` still returns a single array |
| DTO class passed to `payload()` | Resolves shape from `keys()` or public properties |
| Invalid DTO class passed | Throws `InvalidArgumentException` |

## Testing

```bash
composer test
```

## Credits

- [Renato Dehnhardt](https://github.com/rdehnhardt)
- [Josh Donnell](https://github.com/joshdonnell) — for adding the `#[PayloadAttributes]` attribute support
- [All contributors](https://github.com/baconfy/factory-payload/graphs/contributors)

## License

Licensed under the GNU General Public License v3.0 or later (GPL-3.0-or-later). See [LICENSE](LICENSE) for details.