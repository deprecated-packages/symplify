<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\Fixture;

use Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\Source\FirstUsedClass;
use Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\Source\SecondUsedClass;

final class FileUsingOtherClasses
{
    public function run(FirstUsedClass $firstUsedClass): SecondUsedClass
    {
        return new SecondUsedClass();
    }
}
