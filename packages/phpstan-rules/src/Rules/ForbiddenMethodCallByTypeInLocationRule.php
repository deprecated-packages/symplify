<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule\ForbiddenMethodCallByTypeInLocationRuleTest
 */
final class ForbiddenMethodCallByTypeInLocationRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call %s->%s is not allowed in %s';

    /**
     * @var array<string, string[]>
     */
    private $forbiddenTypeInLocations = [];

    /**
     * @param array<string, string[]> $forbiddenTypeInLocations
     */
    public function __construct(array $forbiddenTypeInLocations = [])
    {
        $this->forbiddenTypeInLocations = $forbiddenTypeInLocations;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $variableType = $scope->getType($node->var);
        if (! $variableType instanceof ObjectType) {
            return [];
        }

        $className = $variableType->getClassName();
        if (! array_key_exists($className, $this->forbiddenTypeInLocations)) {
            return [];
        }

        $methodName = $methodCall->name->toString();
        foreach ($this->forbiddenTypeInLocations[$className] as $location) {
            if (Strings::match($className, '#\b' . $location . '\b#')) {
                return [sprintf(self::ERROR_MESSAGE, $className, $methodName, $location)];
            }
        }

        return [];
    }
}
