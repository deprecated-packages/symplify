<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\Variable\VariableNameCollector;
use Symplify\PHPStanRules\ValueObject\VariableNameMetadata;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoDuplicatedVariableCasingNameRule\NoDuplicatedVariableCasingNameRuleTest
 */
final class NoDuplicatedVariableCasingNameRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Lowered variable "%s" is used in various-cased names: "%s", unite it to one';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $run = 1;
    }

    public function go()
    {
        $ruN = 2;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $run = 1;
    }

    public function go()
    {
        $run = 2;
    }
}
CODE_SAMPLE
            ),

        ]);
    }

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
        $variableNamesGroups = $node->get(VariableNameCollector::class);

        $variableMetadataByVariableNames = [];
        $ruleErrors = [];

        foreach ($variableNamesGroups as $filePath => $variableNameGroups) {
            foreach ($variableNameGroups as [$variableName, $line]) {
                // keep only unique names
                /** @var string $variableName */
                $variableMetadataByVariableNames[$variableName] = new VariableNameMetadata(
                    $variableName,
                    $filePath,
                    $line
                );
            }
        }

        $variableMetadataByVariableLowercasedName = [];
        foreach ($variableMetadataByVariableNames as $variableName => $variableMetadata) {
            $variableMetadataByVariableLowercasedName[strtolower($variableName)][] = $variableMetadata;
        }

        foreach ($variableMetadataByVariableLowercasedName as $lowercasedName => $variableNameMetadatas) {
            if (count($variableNameMetadatas) === 1) {
                continue;
            }

            /** @var VariableNameMetadata $variableNameMetadata */
            foreach ($variableNameMetadatas as $variableNameMetadata) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $lowercasedName, $variableNameMetadata->getVariableName());
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($variableNameMetadata->getFilePath())
                    ->line($variableNameMetadata->getLineNumber())
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
