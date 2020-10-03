<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\RequireMethodCallArgumentConstantRule\RequireMethodCallArgumentConstantRuleTest
 */
final class RequireMethodCallArgumentConstantRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call argument on position %d must use constant over value';

    /**
     * @var mixed[]
     */
    private $constantArgByMethodByType = [];

    /**
     * @param mixed[] $constantArgByMethodByType
     */
    public function __construct(array $constantArgByMethodByType = [])
    {
        $this->constantArgByMethodByType = $constantArgByMethodByType;
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
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $errorMessages = [];

        $methodName = (string) $node->name;

        foreach ($this->constantArgByMethodByType as $type => $positionsByMethods) {
            $positions = $this->matchPositions($node, $scope, $type, $positionsByMethods, $methodName);
            if ($positions === null) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $key);
            }
        }

        return $errorMessages;
    }

    private function isNodeVarType(MethodCall $methodCall, Scope $scope, string $desiredType): bool
    {
        $methodVarType = $scope->getType($methodCall->var);
        if (! $methodVarType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($methodVarType->getClassName(), $desiredType, true);
    }

    /**
     * @return mixed|null
     */
    private function matchPositions(
        $node,
        Scope $scope,
        string $desiredType,
        array $positionsByMethods,
        string $methodName
    ) {
        if (! $this->isNodeVarType($node, $scope, $desiredType)) {
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

        if ($arg->value instanceof Variable) {
            return true;
        }

        return $arg->value instanceof ClassConstFetch;
    }
}
