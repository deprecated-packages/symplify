<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Source\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;

final class ActiveFilter implements FilterInterface
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        return sprintf('%s.is_active = 1', $targetTableAlias);
    }
}
