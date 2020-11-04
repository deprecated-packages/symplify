<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenConstructorDependencyByTypeRule\ForbiddenConstructorDependencyByTypeRuleTest
 */
final class ForbiddenConstructorDependencyByTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Object instance of "%s" is forbidden to be passed to constructor';

    /**
     * @var string[]
     */
    private $forbiddenTypes = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param string[] $forbiddenTypes
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $forbiddenTypes = [])
    {
        $this->forbiddenTypes = $forbiddenTypes;
        $this->simpleNameResolver = $simpleNameResolver;
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
        if ($scope->getClassReflection() === null) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->name, MethodName::CONSTRUCTOR)) {
            return [];
        }

        $params = $node->params;
        foreach ($params as $param) {
            if (! $param->type instanceof Name) {
                continue;
            }

            $paramType = $param->type->toString();
            $forbiddenType = $this->getForbiddenType($paramType);
            if ($forbiddenType === null) {
                continue;
            }

            return [sprintf(self::ERROR_MESSAGE, $forbiddenType)];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'

CODE_SAMPLE
                ,
                [
                    'forbiddenTypes' => ['EntityManager'],
                ]
            ),
        ]);
    }

    private function getForbiddenType(string $paramType): ?string
    {
        foreach ($this->forbiddenTypes as $forbiddenType) {
            if (is_a($paramType, $forbiddenType, true)) {
                return $forbiddenType;
            }
        }

        return null;
    }
}
