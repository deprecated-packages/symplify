<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoConstantInterfaceRule\NoConstantInterfaceRuleTest
 */
final class NoConstantInterfaceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Reserve interface for contract only. Move constant holder to a class soon-to-be Enum';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Interface_::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentFullyQualifiedClassName = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($currentFullyQualifiedClassName === null) {
            return [];
        }

        $methodName = (string) $node->name;

        foreach ($this->forbiddenClassMethods as $class => $methods) {
            if (! is_a($currentFullyQualifiedClassName, $class, true)) {
                continue;
            }

            if (in_array($methodName, $methods, true) && $this->hasNewInside($node)) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $currentFullyQualifiedClassName, $methodName);
                return [$errorMessage];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use PHPStan\Rules\Rule;

class SomeRuleTest implements Rule
{
    protected function getRule(): Rule
    {
        return new SomeRule();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPStan\Rules\Rule;

class SomeRuleTest implements Rule
{
    protected function getRule(): Rule
    {
        return $this->getService(SomeRule::class);
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenClassMethods' => [
                        Rule::class => ['getRule'],
                    ],
                ]
            ),
        ]);
    }

    private function hasNewInside(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirstInstanceOf($classMethod, New_::class);
    }
}
