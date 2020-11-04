<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule\ForbiddenPrivateMethodByTypeRuleTest
 */
final class ForbiddenPrivateMethodByTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'private method "%s" in "%s" is not allowed.';

    /**
     * @var array<string, string[]>
     */
    private $forbiddenTypes = [];

    /**
     * @param array<string, string[]> $forbiddenTypes
     */
    public function __construct(NodeFinder $nodeFinder, array $forbiddenTypes = [])
    {
        $this->forbiddenTypes = $forbiddenTypes;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isPrivate()) {
            return [];
        }

        $className = $this->resolveCurrentClassName($node);
        if ($className === null) {
            return [];
        }

        if (! in_array($className, $this->forbiddenTypes, true)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, (string) $node->name, $className)];
    }
}
