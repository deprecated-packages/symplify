<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source;

use Psr\Container\ContainerInterface;

new class() implements ContainerInterface {
    public function get($id): bool
    {
        return true;
    }

    public function has($id): bool
    {
        return false;
    }
};
