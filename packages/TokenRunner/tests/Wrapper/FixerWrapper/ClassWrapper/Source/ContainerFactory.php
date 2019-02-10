<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source;

use Psr\Container\ContainerInterface;

new class() implements ContainerInterface {
    public function get($id): bool
    {
    }

    public function has($id): bool
    {
    }
};
