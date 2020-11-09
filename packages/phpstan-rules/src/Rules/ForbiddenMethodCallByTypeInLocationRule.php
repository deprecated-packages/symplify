<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule\ForbiddenMethodCallByTypeInLocationRuleTest
 */
final class ForbiddenMethodCallByTypeInLocationRule extends AbstractSymplifyRule
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

        $objectTypeName = $variableType->getClassName();
        $className = $objectTypeName;

        if (! array_key_exists($objectTypeName, $this->forbiddenTypeInLocations)) {
            return [];
        }

        foreach (array_keys($this->forbiddenTypeInLocations) as $type) {
            if (Strings::match($objectTypeName, '#\b' . $type . '\b#')) {
                $objectTypeName = $type;
                break;
            }
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $name = $classReflection->getName();

        /** @var Identifier $methodIdentifier */
        $methodIdentifier = $node->name;
        $methodName = $methodIdentifier->toString();

        foreach ($this->forbiddenTypeInLocations[$objectTypeName] as $location) {
            if (Strings::match($name, '#\b' . $location . '\b#')) {
                return [sprintf(self::ERROR_MESSAGE, $className, $methodName, $location)];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $description = sprintf(self::ERROR_MESSAGE, '"ClassName"', '"method"', '"Location"');

        return new RuleDefinition($description, [
            new CodeSample(
                <<<'CODE_SAMPLE'
namespace App\Controller;

use View\Helper;

final class AlbumController
{
    public function get()
    {
        $helper = new Helper();
        $helper->number(4);

        return render();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Controller;

final class AlbumController
{
    public function get()
    {
        return render();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
