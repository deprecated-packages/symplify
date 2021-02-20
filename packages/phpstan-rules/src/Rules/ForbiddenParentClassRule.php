<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenParentClassRule\ForbiddenParentClassRuleTest
 */
final class ForbiddenParentClassRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Inheritance from "%s" class is forbidden. Use "%s" instead';

    /**
     * @var string
     */
    public const COMPOSITION_OVER_INHERITANCE = 'composition over inheritance';

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var string[]
     */
    private $forbiddenParentClasses = [];

    /**
     * @var array<string, string>
     */
    private $forbiddenParentClassesWithPreferences = [];

    /**
     * @param string[] $forbiddenParentClasses
     * @param array<string, string> $forbiddenParentClassesWithPreferences
     */
    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        SimpleNameResolver $simpleNameResolver,
        array $forbiddenParentClasses = [],
        array $forbiddenParentClassesWithPreferences = []
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->simpleNameResolver = $simpleNameResolver;

        $this->forbiddenParentClasses = $forbiddenParentClasses;
        $this->forbiddenParentClassesWithPreferences = $forbiddenParentClassesWithPreferences;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $className = $this->simpleNameResolver->getName($node);
        if ($className === null) {
            return [];
        }

        if ($node->extends === null) {
            return [];
        }

        // no parent
        $currentParentClass = $this->simpleNameResolver->getName($node->extends);
        if ($currentParentClass === null) {
            return [];
        }

        return $this->processParentClass($currentParentClass);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends ParentClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(DecoupledClass $decoupledClass)
    {
        $this->decoupledClass = $decoupledClass;
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenParentClasses' => ['ParentClass'],
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processParentClass(string $currentParentClass): array
    {
        $errorMessages = [];
        foreach ($this->forbiddenParentClasses as $forbiddenParentClass) {
            if (! $this->arrayStringAndFnMatcher->isMatch($currentParentClass, [$forbiddenParentClass])) {
                continue;
            }

            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $currentParentClass, self::COMPOSITION_OVER_INHERITANCE);
        }

        foreach ($this->forbiddenParentClassesWithPreferences as $forbiddenParentClass => $preferredClass) {
            if (! $this->arrayStringAndFnMatcher->isMatch($currentParentClass, [$forbiddenParentClass])) {
                continue;
            }

            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $currentParentClass, $preferredClass);
        }

        return $errorMessages;
    }
}
