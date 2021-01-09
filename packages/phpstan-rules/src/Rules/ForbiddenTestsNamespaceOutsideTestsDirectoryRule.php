<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Location\DirectoryChecker;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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
     * @var string
     */
    private const DESCRIPTION = '"Tests" namespace can be only in "/tests" directory';

    /**
     * @var DirectoryChecker
     */
    private $directoryChecker;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(DirectoryChecker $directoryChecker, SimpleNameResolver $simpleNameResolver)
    {
        $this->directoryChecker = $directoryChecker;
        $this->simpleNameResolver = $simpleNameResolver;
    }

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
        if (! $this->simpleNameResolver->isNameMatch($node, Regex::TESTS_PART_REGEX)) {
            return [];
        }

        if ($this->directoryChecker->isInDirectoryNamed($scope, 'tests')) {
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
