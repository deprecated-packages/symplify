<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\DeadCode;

use Nette\Utils\Arrays;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassMethod\MethodCallCollector;
use Symplify\PHPStanRules\Collector\ClassMethod\PublicClassMethodCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\UnusedPublicClassMethodRuleTest
 */
final class UnusedPublicClassMethodRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class method "%s()" is never used outside of its class';
    public const TIP_MESSAGE = 'Either reduce the methods visibility or annotate it with @api.';

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
        $methodCallCollector = $node->get(MethodCallCollector::class);
        $publicClassMethodCollector = $node->get(PublicClassMethodCollector::class);

        $ruleErrors = [];

        foreach ($publicClassMethodCollector as $filePath => $declarations) {
            foreach ($declarations as [$className, $methodName, $line]) {
                if ($this->isClassMethod($className, $methodName, $methodCallCollector)) {
                    continue;
                }

                /** @var string $methodName */
                $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);

                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($filePath)
                    ->line($line)
                    ->tip(self::TIP_MESSAGE)
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
final class Car
{
    public function turn()
    {
    }

    public function stay()
    {
    }
}

final class Driver
{
    public function driveCar(Car $car)
    {
        $car->turn();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Car
{
    public function turn()
    {
    }
}

final class Driver
{
    public function driveCar(Car $car)
    {
        $car->turn();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param mixed[] $usedClassMethods
     */
    private function isClassMethod(string $className, string $constantName, array $usedClassMethods): bool
    {
        $publicMethodReference = $className . '::' . $constantName;
        $usedClassMethods = Arrays::flatten($usedClassMethods);

        return in_array($publicMethodReference, $usedClassMethods, true);
    }
}
