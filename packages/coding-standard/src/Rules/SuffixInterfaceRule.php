<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\SuffixInterfaceRule\SuffixInterfaceRuleTest
 */
final class SuffixInterfaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface name "%s" must be suffixed with "Interface"';

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
        $interfaceName = (string) $node->name;
        if (Strings::endsWith($interfaceName, 'Interface')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $interfaceName)];
    }
}
