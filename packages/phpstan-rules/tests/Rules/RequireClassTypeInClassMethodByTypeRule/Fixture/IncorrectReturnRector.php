<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Core\Contract\Rector\PhpRectorInterface;
use Rector\Core\RectorDefinition\RectorDefinition;
use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\SomeType;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class IncorrectReturnRector implements PhpRectorInterface
{
    public function getNodeTypes(): array
    {
        return [SomeType::class, Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
    }

    public function beforeTraverse(array $nodes)
    {
    }

    public function enterNode(Node $node)
    {
    }

    public function leaveNode(Node $node)
    {
    }

    public function afterTraverse(array $nodes)
    {
    }

    public function refactor(Node $node): ?Node
    {
    }

    public function getDefinition(): RectorDefinition
    {
    }
}
