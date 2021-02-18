<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Useful for prefixed phar bulid, to keep original references to class un-prefixed
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\RequireStringArgumentInConstructorRuleTest
 */
final class RequireStringArgumentInConstructorRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use quoted string in constructor "new %s()" argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.';

    /**
     * @var array<string, array<int>>
     */
    private $stringArgPositionsByType = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, array<int>> $stringArgPositionsByType
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $stringArgPositionsByType = [])
    {
        $this->stringArgPositionsByType = $stringArgPositionsByType;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        foreach ($this->stringArgPositionsByType as $type => $positions) {
            if (! $this->isNodeVarType($node, $scope, $type)) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $type, $key);
            }
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class AnotherClass
{
    public function run()
    {
        new SomeClass(YetAnotherClass:class);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class AnotherClass
{
    public function run()
    {
        new SomeClass('YetAnotherClass');
    }
}
CODE_SAMPLE
                ,
                [
                    'stringArgPositionsByType' => [
                        'SomeClass' => [0],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param int[] $positions
     */
    private function shouldSkipArg(int $key, array $positions, Arg $arg): bool
    {
        if (! in_array($key, $positions, true)) {
            return true;
        }

        if ($arg->value instanceof String_) {
            return true;
        }

        $classConstFetch = $arg->value;
        if (! $classConstFetch instanceof ClassConstFetch) {
            return true;
        }

        return ! $this->simpleNameResolver->isName($classConstFetch->name, 'class');
    }

    private function isNodeVarType(New_ $constructCall, Scope $scope, string $desiredType): bool
    {
        if (trait_exists($desiredType)) {
            $message = sprintf(
                'Do not use trait "%s" as type to match, it breaks the matching. Use specific class that is in this trait',
                $desiredType
            );

            throw new ShouldNotHappenException($message);
        }

        $constructCallType = $scope->getType($constructCall);
        if (! $constructCallType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($constructCallType->getClassName(), $desiredType, true);
    }
}
