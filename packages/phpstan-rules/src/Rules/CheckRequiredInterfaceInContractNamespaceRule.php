<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule\CheckRequiredInterfaceInContractNamespaceRuleTest
 */
final class CheckRequiredInterfaceInContractNamespaceRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface must be located in "Contract" namespace';

    /**
     * @var string
     * @see https://regex101.com/r/kmrIG1/1
     */
    private const A_CONTRACT_NAMESPACE_REGEX = '#\bContract\b#';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Interface_::class;
    }

    /**
     * @param Interface_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Namespace_|null $namespace */
        $namespace = $node->getAttribute(AttributeKey::PARENT);
        if (! $namespace instanceof Namespace_) {
            return [];
        }

        $namespaceName = (string) $namespace->name;
        if (Strings::match($namespaceName, self::A_CONTRACT_NAMESPACE_REGEX)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
namespace App\Repository;

interface ProductRepositoryInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
