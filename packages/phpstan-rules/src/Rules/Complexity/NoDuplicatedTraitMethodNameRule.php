<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassLike\TraitMethodNameCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedTraitMethodNameRule\NoDuplicatedTraitMethodNameRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class NoDuplicatedTraitMethodNameRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method name "%s()" is used in multiple traits. Make it unique to avoid conflicts';

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
        $traitMethodNameCollector = $node->get(TraitMethodNameCollector::class);

        $traitsByMethodName = [];
        foreach ($traitMethodNameCollector as $fileName => $collectedData) {
            foreach ($collectedData as [$traitNames, $line]) {
                foreach ($traitNames as $traitName) {
                    /** @var string $traitName */
                    $traitsByMethodName[$traitName][] = $fileName;
                }
            }
        }

        return $this->createRuleErrors($traitsByMethodName);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
trait FirstTrait
{
    public function run()
    {
    }
}

trait SecondTrait
{
    public function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
trait FirstTrait
{
    public function run()
    {
    }
}

trait SecondTrait
{
    public function fly()
    {
    }
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @param array<string, string[]> $traitsByMethodName
     * @return RuleError[]
     */
    private function createRuleErrors(array $traitsByMethodName): array
    {
        $ruleErrors = [];

        foreach ($traitsByMethodName as $traitMethodName => $traitFiles) {
            if (count($traitFiles) === 1) {
                continue;
            }

            foreach ($traitFiles as $traitFile) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $traitMethodName);
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($traitFile)
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
