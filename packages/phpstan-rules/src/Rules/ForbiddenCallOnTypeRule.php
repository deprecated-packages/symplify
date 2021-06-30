<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Astral\Naming\SimpleNameResolver;
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
    private array $types = [];

    /**
     * @param array<string, string> $types
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        array $types = []
    ) {
        $this->types = $types;
    }

    /**
     * @return array<class-string<Node>>
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
        /** @var string|null $typeCaller */
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
use Symfony\Component\DependencyInjection\Container;

class SomeClass
{
    /**
     * @var Container
     */
    private $some;

    public function __construct(Container $some)
    {
        $this->some = $some;
    }

    public function call()
    {
        $this->some->call();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Other\SpecificService;

class SomeClass
{
    /**
     * @var SpecificService
     */
    private $specificService;

    public function __construct(SpecificService $specificService)
    {
        $this->specificService = $specificService;
    }

    public function call()
    {
        $this->specificService->call();
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenTypes' => [Container::class],
                ]
            ),
        ]);
    }

    private function getType(MethodCall | StaticCall $node, Scope $scope): ?string
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
