<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverIdenticalCompareRule\Fixture;

use PhpParser\Node;

final class ARector extends AbstractRector
{
    public function refactor(Node $node): ?Node
    {
        $this->getName($node) === 'hey';
        $this->getName($node) !== 'hey';
        return null;
    }
}
