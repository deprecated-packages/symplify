<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\NarrowType;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassMethod\PublicClassMethodParamTypesCollector;
use Symplify\PHPStanRules\Collector\MethodCall\MethodCallArgTypesCollector;
use Symplify\PHPStanRules\Enum\Types\ResolvedTypes;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\NarrowPublicClassMethodParamTypeByCallerTypeRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class NarrowPublicClassMethodParamTypeByCallerTypeRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameters should use "%s" types as the only types passed to this method';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $publicClassMethodCollector = $node->get(PublicClassMethodParamTypesCollector::class);

        $classMethodReferenceToArgTypes = $this->resolveClassMethodReferenceToArgTypes($node);

        $ruleErrors = [];

        foreach ($publicClassMethodCollector as $filePath => $declarations) {
            foreach ($declarations as [$className, $methodName, $paramTypesString, $line]) {
                $uniqueCollectedArgTypesString = $this->resolveUniqueArgTypesString(
                    $classMethodReferenceToArgTypes,
                    $className,
                    $methodName
                );

                if ($uniqueCollectedArgTypesString === null) {
                    continue;
                }

                if ($paramTypesString === $uniqueCollectedArgTypesString) {
                    continue;
                }

                $errorMessage = sprintf(self::ERROR_MESSAGE, $uniqueCollectedArgTypesString);
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($filePath)
                    ->line($line)
                    ->build();
            }
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node\Expr\MethodCall;

final class SomeClass
{
    public function run(SomeService $someService, MethodCall $methodCall)
    {
        $someService->isCheck($node);
    }
}

final class SomeService
{
    public function isCheck($methodCall)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node\Expr\MethodCall;

final class SomeClass
{
    public function run(SomeService $someService, MethodCall $methodCall)
    {
        $someService->isCheck($node);
    }
}

final class SomeService
{
    public function isCheck(MethodCall $methodCall)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<string, string[]>
     */
    private function resolveClassMethodReferenceToArgTypes(CollectedDataNode $collectedDataNode): array
    {
        $methodCallArgTypesByFilePath = $collectedDataNode->get(MethodCallArgTypesCollector::class);

        // group call references and types
        $classMethodReferenceToTypes = [];

        foreach ($methodCallArgTypesByFilePath as $methodCallArgTypes) {
            foreach ($methodCallArgTypes as [$classMethodReference, $argTypesString]) {
                $classMethodReferenceToTypes[$classMethodReference][] = $argTypesString;
            }
        }

        // resolve unique values
        foreach ($classMethodReferenceToTypes as $key => $value) {
            $classMethodReferenceToTypes[$key] = array_unique($value);
        }

        return $classMethodReferenceToTypes;
    }

    /**
     * @param array<string, string[]> $classMethodReferenceToArgTypes
     */
    private function resolveUniqueArgTypesString(
        array $classMethodReferenceToArgTypes,
        string $className,
        string $methodName
    ): ?string {
        $currentClassMethodReference = $className . '::' . $methodName;

        $collectedArgTypes = $classMethodReferenceToArgTypes[$currentClassMethodReference] ?? null;
        if ($collectedArgTypes === null) {
            return null;
        }

        // we need exactly one type
        if (count($collectedArgTypes) !== 1) {
            return null;
        }

        // one of the arg types could not be resolved, we're not sure
        if (in_array(ResolvedTypes::UNKNOWN_TYPES, $collectedArgTypes, true)) {
            return null;
        }

        return $collectedArgTypes[0];
    }
}
