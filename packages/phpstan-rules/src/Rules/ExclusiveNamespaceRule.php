<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule\ExclusiveNamespaceRuleTest
 */
final class ExclusiveNamespaceRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Namespace "%s" is only reserved for "%s". Move the class somewhere else';

    /**
     * @var string
     * @see https://regex101.com/r/EWkKKs/1
     */
    private const EXCLUDED_SUFFIX_REGEX = '#(Test|Trait)$#';

    /**
     * @see https://regex101.com/r/HrTNSI/1
     * @var string
     */
    private const EXCLUDED_NAMESPACE_REGEX = '#\\\\(Exception|Contract)\\\\#';

    /**
     * @param string[] $namespaceParts
     */
    public function __construct(
        private array $namespaceParts
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        $className = $classReflection->getName();

        // skip interface and tests, except tests here
        if (Strings::match($className, self::EXCLUDED_NAMESPACE_REGEX)) {
            return [];
        }

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        foreach ($this->namespaceParts as $namespacePart) {
            if (! \str_ends_with($namespace, $namespacePart)) {
                continue;
            }

            if (Strings::match($className, self::EXCLUDED_SUFFIX_REGEX)) {
                continue;
            }

            if (\str_ends_with($className, $namespacePart)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $namespace, $namespacePart);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Exclusive namespace can only contain classes of specific type, nothing else', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App\Presenter;

class SomeRepository
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Presenter;

class SomePresenter
{
}
CODE_SAMPLE
                ,
                [
                    'namespaceParts' => ['Presenter'],
                ]
            ),
        ]);
    }
}
