<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule\CheckNotTestsNamespaceOutsideTestsDirectoryRuleTest
 */
final class CheckNotTestsNamespaceOutsideTestsDirectoryRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = '"*Test.php" file cannot be located outside "Tests" namespace';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Namespace_::class;
    }

    /**
     * @param Namespace_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->simpleNameResolver->isNameMatch($node, Regex::TESTS_PART_REGEX)) {
            return [];
        }

        if (! \str_ends_with($scope->getFile(), 'Test.php')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// file: "SomeTest.php
namespace App;

class SomeTest
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// file: "SomeTest.php
namespace App\Tests;

class SomeTest
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
