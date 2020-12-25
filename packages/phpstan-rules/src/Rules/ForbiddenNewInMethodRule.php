<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\ForbiddenNewInMethodRuleTest
 */
final class ForbiddenNewInMethodRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"new" in method "%s->%s()" is not allowed.';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var array<string, string[]>
     */
    private $forbiddenClassMethods = [];

    /**
     * @param array<string, string[]> $forbiddenClassMethods
     */
    public function __construct(NodeFinder $nodeFinder, array $forbiddenClassMethods = [])
    {
        $this->nodeFinder = $nodeFinder;
        $this->forbiddenClassMethods = $forbiddenClassMethods;
    }

    /**
     * @return string[]
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
        $currentFullyQualifiedClassName = $this->getClassName($scope);
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
