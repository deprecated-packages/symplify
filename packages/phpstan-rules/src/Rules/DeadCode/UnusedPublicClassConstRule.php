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
use Symplify\PHPStanRules\Collector\ClassConst\ClassConstFetchCollector;
use Symplify\PHPStanRules\Collector\ClassConst\PublicClassLikeConstCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\UnusedPublicClassConstRuleTest
 */
final class UnusedPublicClassConstRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class constant "%s" is never used outside of its class';
    private const TIP_MESSAGE = 'Either reduce its visibility or mark it with @api.';

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
                        ->tip(self::TIP_MESSAGE)
                        ->build();
                }
            }
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class Direction
{
    public LEFT = 'left';

    public RIGHT = 'right';

    public STOP = 'stop';
}

if ($direction === Direction::LEFT) {
    echo 'left';
}

if ($direction === Direction::RIGHT) {
    echo 'right';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Direction
{
    public LEFT = 'left';

    public RIGHT = 'right';
}

if ($direction === Direction::LEFT) {
    echo 'left';
}

if ($direction === Direction::RIGHT) {
    echo 'right';
}
CODE_SAMPLE
            ),
        ]);
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
