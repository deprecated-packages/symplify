<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule\CheckRequiredInterfaceInContractNamespaceRuleTest
 */
final class CheckRequiredInterfaceInContractNamespaceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface is required in Contract namespace';

    /**
     * @var string
     * @see https://regex101.com/r/kmrIG1/1
     */
    private const A_CONTRACT_NAMESPACE_REGEX = '#\bContract\b#';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Interface_::class];
    }

    /**
     * @param Interface_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Namespace_|null */
        $namespace = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $namespace instanceof Namespace_) {
            return [];
        }

        $namespaceName = (string) $namespace->name;
        if (Strings::match($namespaceName, self::A_CONTRACT_NAMESPACE_REGEX)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
