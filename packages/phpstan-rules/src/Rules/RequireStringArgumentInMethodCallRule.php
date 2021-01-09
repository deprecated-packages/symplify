<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Useful for prefixed phar bulid, to keep original references to class un-prefixed
 *
 * Basically inversion of this rule:
 * @see https://github.com/symplify/symplify/tree/master/packages/coding-standard#defined-method-argument-should-be-always-constant-value
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\RequireStringArgumentInMethodCallRuleTest
 */
final class RequireStringArgumentInMethodCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use quoted string in method call "%s()" argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.';

    /**
     * @var array<string, array<string, array<int>>>
     */
    private $stringArgByMethodByType = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, array<string, array<int>>> $stringArgByMethodByType
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $stringArgByMethodByType = [])
    {
        $this->stringArgByMethodByType = $stringArgByMethodByType;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methodCallName = $this->simpleNameResolver->getName($node->name);
        if ($methodCallName === null) {
            return [];
        }

        $errorMessages = [];

        foreach ($this->stringArgByMethodByType as $type => $positionsByMethods) {
            $positions = $this->matchPositions($node, $scope, $type, $positionsByMethods, $methodCallName);
            if ($positions === null) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $methodCallName, $key);
            }
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class AnotherClass
{
    public function run(SomeClass $someClass)
    {
        $someClass->someMethod(YetAnotherClass:class);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class AnotherClass
{
    public function run(SomeClass $someClass)
    {
        $someClass->someMethod('YetAnotherClass'');
    }
}
CODE_SAMPLE
                ,
                [
                    'stringArgByMethodByType' => [
                        'SomeClass' => [
                            'someMethod' => [0],
                        ],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param array<string, array<int>> $positionsByMethods
     */
    private function matchPositions(
        MethodCall $methodCall,
        Scope $scope,
        string $desiredType,
        array $positionsByMethods,
        string $methodName
    ): ?array {
        if (! $this->isNodeVarType($methodCall, $scope, $desiredType)) {
            return null;
        }

        return $positionsByMethods[$methodName] ?? null;
    }

    /**
     * @param int[] $positions
     */
    private function shouldSkipArg(int $key, array $positions, Arg $arg): bool
    {
        if (! in_array($key, $positions, true)) {
            return true;
        }

        if ($arg->value instanceof String_) {
            return true;
        }

        /** @var ClassConstFetch $classConstFetch */
        $classConstFetch = $arg->value;

        return ! $this->simpleNameResolver->isName($classConstFetch->name, 'class');
    }

    private function isNodeVarType(MethodCall $methodCall, Scope $scope, string $desiredType): bool
    {
        if (trait_exists($desiredType)) {
            $message = sprintf(
                'Do not use trait "%s" as type to match, it breaks the matching. Use specific class that is in this trait',
                $desiredType
            );

            throw new ShouldNotHappenException($message);
        }

        $methodVarType = $scope->getType($methodCall->var);
        if (! $methodVarType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($methodVarType->getClassName(), $desiredType, true);
    }
}
