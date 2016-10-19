<?php

declare (strict_types = 1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularDoctrineFilters\Contract\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;

interface FilterInterface
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias) : string;
}
