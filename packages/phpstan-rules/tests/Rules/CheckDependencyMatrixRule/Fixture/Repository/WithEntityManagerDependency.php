<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckDependencyMatrixRule\Fixture\Repository;

class WithEntityManagerDependency
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
}
