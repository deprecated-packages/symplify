<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassMethod\ClassMethodContentCollector;
use Symplify\PHPStanRules\ValueObject\Metadata\ClassMethodMetadata;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PreventDuplicateClassMethodRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s()" is duplicated. Use unique content or service instead';

    public function __construct(
        private int $minimumLineCount = 3,
    ) {
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
        $classMethodsContentByFile = $node->get(ClassMethodContentCollector::class);

        $ruleErrors = [];

        $classMethodMetadatasByContentsHash = $this->groupClassMethodMetadatasByContentsHash(
            $classMethodsContentByFile
        );
        foreach ($classMethodMetadatasByContentsHash as $classMethodMetadatas) {
            // keep only long enough methods
            $classMethodMetadatas = array_filter(
                $classMethodMetadatas,
                fn (ClassMethodMetadata $classMethodMetadata): bool => $classMethodMetadata->getLineCount() >= $this->minimumLineCount
            );

            // method is unique, we can skip it
            if (count($classMethodMetadatas) === 1) {
                continue;
            }

            // report errors
            /** @var ClassMethodMetadata $classMethodMetadata */
            foreach ($classMethodMetadatas as $classMethodMetadata) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $classMethodMetadata->getMethodName());

                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($classMethodMetadata->getFileName())
                    ->line($classMethodMetadata->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}

class AnotherClass
{
    public function someMethod()
    {
        echo 'statement';
        $differentValue = new SmartFinder();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}
}
CODE_SAMPLE
                ,
                [
                    'minimumLineCount' => 3,
                ]
            ),
        ]);
    }

    /**
     * @param mixed[] $classMethodsContentByFile
     * @return array<string, ClassMethodMetadata[]>
     */
    private function groupClassMethodMetadatasByContentsHash(array $classMethodsContentByFile): array
    {
        $methodsNamesAndFilesByMethodContents = [];

        foreach ($classMethodsContentByFile as $fileName => $classMethodContents) {
            foreach ($classMethodContents as [$methodName, $methodLine, $methodContents]) {
                $methodContentsHash = md5($methodContents);

                $methodLineCount = substr_count($methodContents, "\n");

                $methodsNamesAndFilesByMethodContents[$methodContentsHash][] = new ClassMethodMetadata(
                    $methodName,
                    $methodLineCount,
                    $fileName,
                    $methodLine,
                );
            }
        }

        return $methodsNamesAndFilesByMethodContents;
    }
}
