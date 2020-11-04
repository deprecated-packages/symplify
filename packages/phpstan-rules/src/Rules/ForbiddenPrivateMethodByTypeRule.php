<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule\ForbiddenPrivateMethodByTypeRuleTest
 */
final class ForbiddenPrivateMethodByTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'private method in type "%s" is not allowed.';

    /**
     * @var array<string, string>
     */
    private $forbiddenTypes = [];

    /**
     * @param array<string, string> $forbiddenTypes
     */
    public function __construct(array $forbiddenTypes = [])
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

        foreach ($this->forbiddenTypes as $forbiddenType) {
            if (! is_a($className, $forbiddenType, true)) {
                continue;
            }

            return [sprintf(self::ERROR_MESSAGE, $forbiddenType)];
        }

        return [];
    }
}
