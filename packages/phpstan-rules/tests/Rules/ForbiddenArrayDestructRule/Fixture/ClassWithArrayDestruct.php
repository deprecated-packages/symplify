<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\Fixture;

final class ClassWithArrayDestruct
{
    public function run()
    {
        [$one, $two] = $this->getResult();
    }

    public function getResult()
    {
        return [1, 2];
    }
}
