<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
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
    public const ERROR_MESSAGE = '"*Test.php" file cannot be located outside "Tests" namespace';

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
        if (! $node->name instanceof Name) {
            return [];
        }

        $matches = Strings::match($node->name->toString(), Regex::TESTS_PART_REGEX);
        if ($matches !== null) {
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

// file: "AnotherTest.php
namespace App\Tests\Features;

class AnotherTest
{
}

// file: "SomeOtherTest.php
namespace Tests\Features;

class SomeOtherTest
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
