<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverIdenticalCompareRule\Fixture;

use PhpParser\Node;

final class DependencyInjectionRector
{
    private $rector;

    public function __construct(AbstractRector $rector)
    {
        $this->rector = $rector;
    }

    public function refactor(Node $node): ?Node
    {
        $this->rector->getName($node) === 'hey';
        $this->rector->getName($node) !== 'hey';
        return null;
    }
}
