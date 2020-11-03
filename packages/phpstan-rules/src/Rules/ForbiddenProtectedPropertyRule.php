<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyser\ProtectedAnalyser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\ForbiddenProtectedPropertyRuleTest
 */
final class ForbiddenProtectedPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with protected modifier is not allowed. Use interface instead.';

    /**
     * @var ProtectedAnalyser
     */
    private $protectedAnalyser;

    public function __construct(ProtectedAnalyser $protectedAnalyser)
    {
        $this->protectedAnalyser = $protectedAnalyser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isProtected()) {
            return [];
        }

        if ($this->protectedAnalyser->isProtectedPropertyOrClassConstAllowed($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
