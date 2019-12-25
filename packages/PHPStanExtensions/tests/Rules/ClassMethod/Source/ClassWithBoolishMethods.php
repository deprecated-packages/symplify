<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod\Source;

final class ClassWithBoolishMethods
{
    public function honesty()
    {
        return true;
    }

    public function thatWasGreat()
    {
        if (rand(1, 3)) {
            return true;
        }

        return false;
    }
}
