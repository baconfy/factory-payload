# Factory Payload

Generate clean HTTP request payloads from Laravel Eloquent factories.

[![Tests](https://github.com/baconfy/factory-payload/actions/workflows/tests.yml/badge.svg)](https://github.com/baconfy/factory-payload/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)
[![License](https://img.shields.io/packagist/l/baconfy/factory-payload.svg)](https://packagist.org/packages/baconfy/factory-payload)

## Why?

When testing HTTP endpoints, you need request payloads that match the shape your endpoint expects — not the shape your model stores.

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

Sometimes you need to control specific fields — for example, to test validation or assert against known values:

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

Requires PHP 8.2+ and Laravel 11, 12 or 13.

## Usage

Add the `HasPayload` trait to your factory and declare which attributes belong in the HTTP payload:

```php
namespace Database\Factories;

use App\Models\Post;
use Baconfy\FactoryPayload\HasPayload;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    use HasPayload;

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

Then in your tests:

```php
$payload = Post::factory()->payload();
// ['title' => 'Lorem ipsum...', 'body' => 'Dolor sit amet...']
```

Notice how `user_id` and `published_at` — fields that belong to persistence, not to the HTTP request — are automatically excluded.

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

| Scenario | Result |
|----------|--------|
| `$payloadAttributes` not declared or empty | Returns only the overrides |
| `$payloadAttributes` declared | Filters `raw()` by whitelist, then merges overrides |
| Override key exists in whitelist | Override wins |
| Override key not in whitelist | Override still passes through |
| Factory has `count()` set | `payload()` still returns a single array |

## Testing

```bash
composer test
```

## License

Licensed under the GNU General Public License v3.0 or later (GPL-3.0-or-later). See [LICENSE](LICENSE) for details.