<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
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
     * @param array<string, string> $types
     */
    public function __construct(array $types = [])
    {
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
        $type = $scope->getType($node->var);
        if (! $type instanceof ObjectType) {
            return [];
        }

        /** @var string $className */
        $className = $type->getClassName();
        foreach ($this->types as $type) {
            if (is_a($className, $type, true)) {
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
}
