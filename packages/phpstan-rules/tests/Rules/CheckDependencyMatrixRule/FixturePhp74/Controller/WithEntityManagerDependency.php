<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckDependencyMatrixRule\FixturePhp74\Controller;

class WithEntityManagerDependency
{
    private EntityManagerInterface $entityManager;
}
