<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteArrayAccessInControlRule\NoNetteArrayAccessInControlRuleTest
 */
final class NoNetteArrayAccessInControlRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid using magical unclear array access and use explicit "$this->getComponent()" instead';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ArrayDimFetch::class;
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        // this type has getComponent() method
        if (! $classReflection->isSubclassOf('Nette\ComponentModel\Container')) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        $callerVariable = $node->var;
        if (! is_string($callerVariable->name)) {
            return [];
        }

        if ($callerVariable->name !== 'this') {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        return $this['someControl'];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        return $this->getComponent('someControl');
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
