<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Source\FakeUnitOfWork;
use Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Source\MarkerInterface;

final class SkipArrayFilter
{
    public function run(FakeUnitOfWork $fakeUnitOfWork): array
    {
        return array_filter($fakeUnitOfWork->getItems(), function (object $entity) {
            return $entity instanceof MarkerInterface;
        });
    }
}
