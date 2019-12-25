<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod\Source;

final class ClassWithEmptyReturn
{
    public function nothing()
    {
        return;
    }
}
