<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Types\ContainsTypeAnalyser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\RequireConstantInMethodCallPositionRuleTest
 */
final class RequireConstantInMethodCallPositionRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter argument on position %d must use %s constant';

    /**
     * @var ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;

    /**
     * @var array<class-string, mixed[]>
     */
    private $requiredLocalConstantInMethodCall = [];

    /**
     * @var array<class-string, mixed[]>
     */
    private $requiredExternalConstantInMethodCall = [];

    /**
     * @param array<class-string, mixed[]> $requiredLocalConstantInMethodCall
     * @param array<class-string, mixed[]> $requiredExternalConstantInMethodCall
     */
    public function __construct(
        array $requiredLocalConstantInMethodCall = [],
        array $requiredExternalConstantInMethodCall = [],
        ContainsTypeAnalyser $containsTypeAnalyser
    ) {
        $this->requiredLocalConstantInMethodCall = $requiredLocalConstantInMethodCall;
        $this->requiredExternalConstantInMethodCall = $requiredExternalConstantInMethodCall;
        $this->containsTypeAnalyser = $containsTypeAnalyser;
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

        $errorMessagesLocal = $this->getErrorMessages(
            $node,
            $scope,
            true,
            $this->requiredLocalConstantInMethodCall,
            'local'
        );
        $errorMessagesExternal = $this->getErrorMessages(
            $node,
            $scope,
            false,
            $this->requiredExternalConstantInMethodCall,
            'external'
        );

        return array_merge($errorMessagesLocal, $errorMessagesExternal);
    }

    /**
     * @return string[]
     */
    private function getErrorMessages(
        MethodCall $methodCall,
        Scope $scope,
        bool $isLocalConstant,
        array $config,
        string $messageVar
    ): array {
        /** @var Identifier $name */
        $name = $methodCall->name;
        $methodName = (string) $name;
        $errorMessages = [];

        /** @var class-string $type */
        foreach ($config as $type => $positionsByMethods) {
            $positions = $this->matchPositions($methodCall, $scope, $type, $positionsByMethods, $methodName);
            if ($positions === null) {
                continue;
            }

            foreach ($methodCall->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg, $isLocalConstant)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $key, $messageVar);
            }
        }

        return $errorMessages;
    }

    /**
     * @param class-string $desiredType
     * @return mixed|null
     */
    private function matchPositions(
        MethodCall $methodCall,
        Scope $scope,
        string $desiredType,
        array $positionsByMethods,
        string $methodName
    ) {
        if (! $this->containsTypeAnalyser->containsExprTypes($methodCall->var, $scope, [$desiredType])) {
            return null;
        }

        return $positionsByMethods[$methodName] ?? null;
    }

    /**
     * @param int[] $positions
     */
    private function shouldSkipArg(int $key, array $positions, Arg $arg, bool $isLocalConstant): bool
    {
        if (! in_array($key, $positions, true)) {
            return true;
        }

        if ($arg->value instanceof Variable) {
            return true;
        }

        if (! $arg->value instanceof ClassConstFetch) {
            return false;
        }

        return $isLocalConstant
            ? $arg->value->class instanceof Name
            : $arg->value->class instanceof FullyQualified;
    }
}
