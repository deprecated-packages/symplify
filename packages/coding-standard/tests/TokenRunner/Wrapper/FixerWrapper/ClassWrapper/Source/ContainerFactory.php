<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\TokenRunner\Wrapper\FixerWrapper\ClassWrapper\Source;

use Psr\Container\ContainerInterface;

new class() implements ContainerInterface {
    public function get($id)
    {
        return 5;
    }

    public function has($id): bool
    {
        return false;
    }
};
