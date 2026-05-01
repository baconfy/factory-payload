<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload\Tests\Fixtures;

class UserCreateData
{
    /** @return array<int, string> */
    public static function keys(): array
    {
        return ['name', 'email'];
    }
}
