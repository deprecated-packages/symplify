<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#9-do-not-use-getters-and-setters
 *
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule\NoSetterClassMethodRuleTest
 */
final class NoSetterClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter "%s()" is not allowed. Use constructor injection or behavior name instead, e.g. "changeName()"';

    /**
     * @var string
     * @see https://regex101.com/r/IMIpoN/1/
     */
    private const SETTER_REGEX = '#^set[\w+]#';

    /**
     * @var string[]
     */
    private array $allowedSetterClasses = [];

    /**
     * @param string[] $allowedSetterClasses
     */
    public function __construct(array $allowedSetterClasses = [])
    {
        $this->allowedSetterClasses = $allowedSetterClasses;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methodName = (string) $node->name;
        if (! Strings::match($methodName, self::SETTER_REGEX)) {
            return [];
        }

        if ($this->isClassExcluded($scope)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function setName(string $name)
    {
        // ...
    }
}

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function __construct(string $name)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isClassExcluded(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($this->allowedSetterClasses as $allowedClass) {
            if (fnmatch($allowedClass, $classReflection->getName())) {
                return true;
            }
        }

        return false;
    }
}
