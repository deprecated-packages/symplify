<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule\CheckNotTestsNamespaceOutsideTestsDirectoryRuleTest
 */
final class CheckNotTestsNamespaceOutsideTestsDirectoryRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = '"*Test.php" file cannot be located outside "Tests" namespace';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
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
        if ($this->simpleNameResolver->isNameMatch($node, Regex::TESTS_PART_REGEX)) {
            return [];
        }

        if (! Strings::endsWith($scope->getFile(), 'Test.php')) {
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
