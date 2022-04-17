<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Location\DirectoryChecker;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule\ForbiddenTestsNamespaceOutsideTestsDirectoryRuleTest
 */
final class ForbiddenTestsNamespaceOutsideTestsDirectoryRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = '"Tests" namespace cannot be used outside of "tests" directory';

    /**
     * @var string
     */
    private const DESCRIPTION = '"Tests" namespace can be only in "/tests" directory';

    public function __construct(
        private DirectoryChecker $directoryChecker,
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
        if (! $this->simpleNameResolver->isNameMatch($node, Regex::TESTS_PART_REGEX)) {
            return [];
        }

        if ($this->directoryChecker->isInDirectoryNames($scope, ['tests', 'packages-tests'])) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::DESCRIPTION, [
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
