<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Source;

final class FakeUnitOfWork
{
    /**
     * @return array<string, object>
     */
    public function getItems(): array
    {
    }
}
