<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\ForbiddenParentClassRuleTest
 */
final class ForbiddenParentClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" inherits from forbidden parent class "%s". Use composition over inheritance instead';

    /**
     * @var string[]
     */
    private $forbiddenParentClasses = [];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @param string[] $forbiddenParentClasses
     */
    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher, array $forbiddenParentClasses = [])
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->forbiddenParentClasses = $forbiddenParentClasses;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        // no parent
        if ($node->extends === null) {
            return [];
        }

        $parentClass = $node->extends->toString();
        if (! $this->arrayStringAndFnMatcher->isMatch($parentClass, $this->forbiddenParentClasses)) {
            return [];
        }

        $class = $node->namespacedName->toString();

        $errorMessage = sprintf(self::ERROR_MESSAGE, $class, $parentClass);
        return [$errorMessage];
    }
}
