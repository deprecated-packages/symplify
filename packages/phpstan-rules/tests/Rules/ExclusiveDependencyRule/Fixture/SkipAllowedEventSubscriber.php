<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Fixture;

use Doctrine\ORM\EntityManager;
use Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Source\AllowedEventSubscriber;

final class SkipAllowedEventSubscriber implements AllowedEventSubscriber
{
    public function __construct(
        EntityManager $entityManager
    ) {
    }
}
