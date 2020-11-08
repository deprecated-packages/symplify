<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule\ForbiddenTestsNamespaceOutsideTestsDirectoryRuleTest
 */
final class ForbiddenTestsNamespaceOutsideTestsDirectoryRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = '"Tests" namespace cannot be used outside of "tests" directory';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Namespace_::class];
    }

    /**
     * @param Namespace_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->containsNamespace($node, 'Tests')) {
            return [];
        }

        if ($this->isInDirectoryNamed($scope, 'tests')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $description = '"Tests" namespace can be only in "/tests" directory';
        return new RuleDefinition($description, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// file path: "src/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// file path: "tests/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
