<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source;

final class SomeNonDoctrine
{
    public function getRepository(int $id)
    {
    }
}
