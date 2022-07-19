<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassConst\RegexClassConstCollector;
use Symplify\PHPStanRules\ValueObject\ClassConstRegexMetadata;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\NoDuplicatedRegexRule\NoDuplicatedRegexRuleTest
 */
final class NoDuplicatedRegexRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The "%s" constant contains duplicated regex "%s". Instead of duplicated regexes, extract domain regexes together to save maintenance';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private const CLASS_NAME_REGEX = '#[\w\\]+#';
}

class AnotherClass
{
    private const DIFFERENT_NAME_REGEX = '#[\w\\]+#';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class ClassRegexRecipies
{
    private const NAME_REGEX = '#[\w\\]+#';
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
        $regexClassConsts = $node->get(RegexClassConstCollector::class);
        $regexConstMetadatasBySharedRegex = $this->groupConstantsBySharedValue($regexClassConsts);

        return $this->createRuleErrorsForDuplicatedRegexes($regexConstMetadatasBySharedRegex);
    }

    /**
     * @param mixed[] $regexClassConsts
     * @return array<string, ClassConstRegexMetadata[]>
     */
    private function groupConstantsBySharedValue(array $regexClassConsts): array
    {
        $regexConstMetadataBySharedRegex = [];

        foreach ($regexClassConsts as $filePath => $collectedDatas) {
            foreach ($collectedDatas as $collectedData) {
                foreach ($collectedData as [$constName, $regexValue, $line]) {
                    $regexConstMetadataBySharedRegex[$regexValue][] = new ClassConstRegexMetadata(
                        $constName,
                        $regexValue,
                        $filePath,
                        $line
                    );
                }
            }
        }

        return $regexConstMetadataBySharedRegex;
    }

    /**
     * @param array<string, ClassConstRegexMetadata[]> $regexConstMetadatasBySharedRegex
     * @return RuleError[]
     */
    private function createRuleErrorsForDuplicatedRegexes(array $regexConstMetadatasBySharedRegex): array
    {
        $ruleErrors = [];

        foreach ($regexConstMetadatasBySharedRegex as $regexConstMetadatas) {
            if (count($regexConstMetadatas) === 1) {
                continue;
            }

            foreach ($regexConstMetadatas as $regexConstMetadata) {
                $errorMessage = sprintf(
                    self::ERROR_MESSAGE,
                    $regexConstMetadata->getConstantName(),
                    $regexConstMetadata->getRegexValue()
                );

                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->line($regexConstMetadata->getLine())
                    ->file($regexConstMetadata->getFilePath())
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
