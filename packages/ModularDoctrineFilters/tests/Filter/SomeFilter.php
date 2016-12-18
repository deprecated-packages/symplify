<?php

declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;

final class SomeFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias) : string
    {
        return $targetTableAlias . '.enabled=0';
    }
}
