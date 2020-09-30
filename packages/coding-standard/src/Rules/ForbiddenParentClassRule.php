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
    public const ERROR_MESSAGE = 'Class "%s" inherits from forbidden parent class "%s". Use "%s" instead';

    /**
     * @var string
     */
    public const COMPOSITION_OVER_INHERITANCE = 'composition over inheritance';

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var array<string, string|null>
     * Null, if there is no preference. Just forbidden
     */
    private $forbiddenParentClassesWithPreferences = [];

    /**
     * @param string[] $forbiddenParentClasses
     * @param string[] $forbiddenParentClassesWithPreferences
     */
    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        array $forbiddenParentClasses = [],
        array $forbiddenParentClassesWithPreferences = []
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->forbiddenParentClassesWithPreferences = $forbiddenParentClassesWithPreferences;

        foreach ($forbiddenParentClasses as $forbiddenParentClass) {
            $this->forbiddenParentClassesWithPreferences[$forbiddenParentClass] = null;
        }
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

        $currentParentClass = $node->extends->toString();

        foreach ($this->forbiddenParentClassesWithPreferences as $forbiddenParentClass => $preference) {
            if (! $this->arrayStringAndFnMatcher->isMatch($currentParentClass, [$forbiddenParentClass])) {
                continue;
            }

            // allow inheritance
            if ($preference !== null && $node->isAbstract()) {
                continue;
            }

            $class = $node->namespacedName->toString();

            $errorMessage = $this->createErrorMessage($preference, $class, $currentParentClass);
            return [$errorMessage];
        }

        return [];
    }

    private function createErrorMessage(?string $preference, string $class, string $currentParentClass): string
    {
        $preferenceMessage = $preference ?? self::COMPOSITION_OVER_INHERITANCE;

        return sprintf(self::ERROR_MESSAGE, $class, $currentParentClass, $preferenceMessage);
    }
}
