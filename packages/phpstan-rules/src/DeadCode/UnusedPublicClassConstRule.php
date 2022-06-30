<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\DeadCode;

use Nette\Utils\Arrays;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassConstFetchCollector;
use Symplify\PHPStanRules\Collector\PublicClassLikeConstCollector;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\UnusedPublicClassConstRuleTest
 */
final class UnusedPublicClassConstRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class constant "%s" is never used';

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
        $classConstFetchCollector = $node->get(ClassConstFetchCollector::class);
        $publicClassLikeConstCollector = $node->get(PublicClassLikeConstCollector::class);

        $ruleErrors = [];

        foreach ($publicClassLikeConstCollector as $filePath => $declarationsGroups) {
            foreach ($declarationsGroups as $declarationGroup) {
                foreach ($declarationGroup as [$className, $constantName, $line]) {
                    if ($this->isClassConstantUsed($className, $constantName, $classConstFetchCollector)) {
                        continue;
                    }

                    /** @var string $constantName */
                    $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);

                    $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                        ->file($filePath)
                        ->line($line)
                        ->build();
                }
            }
        }

        return $ruleErrors;
    }

    /**
     * @param mixed[] $usedConstFetches
     */
    private function isClassConstantUsed(string $className, string $constantName, array $usedConstFetches): bool
    {
        $publicConstantReference = $className . '::' . $constantName;

        $usedConstFetches = Arrays::flatten($usedConstFetches);
        return in_array($publicConstantReference, $usedConstFetches, true);
    }
}
