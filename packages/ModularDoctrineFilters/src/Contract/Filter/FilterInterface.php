<?php

declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Contract\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;

interface FilterInterface
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias) : string;
}
