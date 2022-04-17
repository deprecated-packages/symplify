<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Useful for prefixed phar build, to keep original references to class un-prefixed
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\RequireStringArgumentInConstructorRuleTest
 */
final class RequireStringArgumentInConstructorRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use quoted string in constructor "new %s()" argument on position %d instead of "::class". It prevent scoping of the class in building prefixed package.';

    /**
     * @param array<class-string, array<int>> $stringArgPositionsByType
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ContainsTypeAnalyser $containsTypeAnalyser,
        private array $stringArgPositionsByType
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        foreach ($this->stringArgPositionsByType as $type => $positions) {
            if (! $this->containsTypeAnalyser->containsExprType($node, $scope, $type)) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if (! $arg instanceof Arg) {
                    continue;
                }

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
}
