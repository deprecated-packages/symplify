<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipValidJustUnionType
{
    public function getData(array $itemIds): array
    {
        return $itemIds ? $this->findBy([
            'i2%in' => $itemIds,
        ]) : [];
    }

    private function findBy($criteria)
    {
    }
}
