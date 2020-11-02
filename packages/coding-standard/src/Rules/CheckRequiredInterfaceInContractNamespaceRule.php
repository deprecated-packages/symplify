<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule\CheckRequiredInterfaceInContractNamespaceRuleTest
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
        return [UseUse::class];
    }

    /**
     * @param UseUse $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $name = (string) $node->name;
        if (interface_exists($name)) {
            return [];
        }

        $namespace = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($namespace) {
            if ($namespace instanceof Namespace_) {
                return $namespace;
            }

            $namespace = $namespace->getAttribute(PHPStanAttributeKey::PARENT);
        }

        if (! $namespace instanceof Namespace_) {
            return [];
        }

        if (! Strings::match((string) $namespace->name, self::A_CONTRACT_NAMESPACE_REGEX)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
