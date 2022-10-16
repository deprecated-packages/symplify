<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Source\CustomEntityManager;

final class SkipSomeRepository
{
    /**
     * @var CustomEntityManager
     */
    private $entityManager;

    public function __construct(CustomEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
