<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule\ForbiddenCallOnTypeRuleTest
 */
final class ForbiddenCallOnTypeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call or Static Call on %s is not allowed';

    /**
     * @var array<string, string>
     */
    private $types = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string> $types
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $types = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->types = $types;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var string $typeCaller */
        $typeCaller = $this->getType($node, $scope);
        if ($typeCaller === null) {
            return [];
        }

        foreach ($this->types as $type) {
            if (is_a($typeCaller, $type, true)) {
                return [sprintf(self::ERROR_MESSAGE, $type)];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
/**
 * @var \Symfony\Component\DependencyInjection\Container
 */
private $some;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @var \Other\Class
 */
private $some;
CODE_SAMPLE
                ,
                [
                    'forbiddenTypes' => ['Symfony\Component\DependencyInjection\Container'],
                ]
            ),
        ]);
    }

    /**
     * @param MethodCall|StaticCall $node
     */
    private function getType(Node $node, Scope $scope): ?string
    {
        if ($node instanceof MethodCall) {
            $type = $scope->getType($node->var);

            if (! $type instanceof ObjectType) {
                return null;
            }

            return $type->getClassName();
        }

        return $this->simpleNameResolver->getName($node->class);
    }
}
