<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\SingleNetteInjectMethodRule\SingleNetteInjectMethodRuleTest
 */
final class SingleNetteInjectMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use single inject*() class method per class';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethodsNode::class];
    }

    /**
     * @param ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (count($node->getMethods()) < 2) {
            return [];
        }

        $classMethodNames = $this->resolveClassMethodNames($node);

        $injectMethodCount = 0;
        foreach ($classMethodNames as $classMethodName) {
            if (Strings::startsWith($classMethodName, 'inject')) {
                ++$injectMethodCount;
            }
        }

        if ($injectMethodCount < 2) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectOne(Type $type)
    {
        $this->type = $type;
    }

    public function injectTwo(AnotherType $anotherType)
    {
        $this->anotherType = $anotherType;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectSomeClass(
        Type $type,
        AnotherType $anotherType
    ) {
        $this->type = $type;
        $this->anotherType = $anotherType;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function resolveClassMethodNames(ClassMethodsNode $classMethodsNode): array
    {
        $methodNames = [];
        foreach ($classMethodsNode->getMethods() as $classMethod) {
            $methodNames[] = $classMethod->name->toString();
        }

        return $methodNames;
    }
}
