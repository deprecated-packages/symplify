<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\Tests\FilterManager\Source;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\FilterInterface;

final class ActiveFilter implements FilterInterface
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias) : string
    {
        return sprintf('%s.is_active = 1', $targetTableAlias);
    }
}
