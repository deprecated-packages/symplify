<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\Contract;

use Doctrine\ORM\Mapping\ClassMetadata;

interface FilterInterface
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias) : string;
}
