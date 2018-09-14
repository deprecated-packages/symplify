<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\Wrapper\FixerWrapper\ClassWrapper\Source;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;

final class SomeClass extends AbstractRector
{
    public function refactor(Node $node): ?Node
    {
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
    }

    public function getDefinition(): RectorDefinition
    {
    }
}
