<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Reflection\MethodCallNodeAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\RequireEnumDocBlockOnConstantListPassRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class RequireEnumDocBlockOnConstantListPassRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'On passing a constant, the method should have an enum type. See https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants';

    public function __construct(
        private MethodCallNodeAnalyzer $methodCallNodeAnalyzer,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // has argument of constant class reference
        if (! $this->hasMethodCallClassConstFetchArg($node)) {
            return [];
        }

        $classMethodReflection = $this->methodCallNodeAnalyzer->resolveMethodCallReflection($node, $scope);
        if (! $classMethodReflection instanceof PhpMethodReflection) {
            return [];
        }

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($classMethodReflection->getVariants());

        foreach ($parametersAcceptor->getParameters() as $parameterReflection) {
            if (! $parameterReflection instanceof PhpParameterReflection) {
                continue;
            }

            dump($parameterReflection->getType());
        }

//        $this->reflectionParser->parseMethodReflection($node);
        // dump($node);
        die;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    private function turn(string $direction)
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    /**
     * @param Direction::*
     */
    private function turn(string $direction)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasMethodCallClassConstFetchArg(MethodCall $methodCall): bool
    {
        foreach ($methodCall->getArgs() as $arg) {
            if ($arg->value instanceof ClassConstFetch) {
                return true;
            }
        }

        return false;
    }
}
