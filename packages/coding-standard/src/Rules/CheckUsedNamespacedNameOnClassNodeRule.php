<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\CheckUsedNamespacedNameOnClassNodeRuleTest
 */
final class CheckUsedNamespacedNameOnClassNodeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use namespaceName on Class_ node';

    /**
     * @var string[]
     */
    private $excludedClasses = [];

    /**
     * @param string[] $excludedClasses
     */
    public function __construct(array $excludedClasses = [])
    {
        $this->excludedClasses = $excludedClasses;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Variable::class];
    }

    /**
     * @param Variable $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node);
        if (! method_exists($type, 'getClassName')) {
            return [];
        }

        if ($type->getClassName() !== Class_::class) {
            return [];
        }

        $next = $node->getAttribute(PHPStanAttributeKey::NEXT);
        if ($next === null) {
            return [];
        }

        if ($next->name !== 'name') {
            return [];
        }

        $class = $this->getClassOfVariable($node);
        if ($class === null) {
            return [];
        }

        if (in_array($class->namespacedName->toString(), $this->excludedClasses, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function getClassOfVariable(Variable $variable): ?Class_
    {
        $class = $variable->getAttribute(PHPStanAttributeKey::PARENT);
        while ($class) {
            if ($class instanceof Class_) {
                return $class;
            }

            $class = $class->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }
}
