<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Matcher\PositionMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\RequireMethodCallArgumentConstantRuleTest
 */
final class RequireMethodCallArgumentConstantRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call argument on position %d must use constant (e.g. "Option::NAME") over value';

    /**
     * @var array<class-string, mixed[]>
     */
    private $constantArgByMethodByType = [];

    /**
     * @var PositionMatcher
     */
    private $positionMatcher;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<class-string, mixed[]> $constantArgByMethodByType
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        PositionMatcher $positionMatcher,
        array $constantArgByMethodByType = []
    ) {
        $this->constantArgByMethodByType = $constantArgByMethodByType;
        $this->positionMatcher = $positionMatcher;
        $this->simpleNameResolver = $simpleNameResolver;
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
        $methodCallName = $this->simpleNameResolver->getName($node->name);
        if ($methodCallName === null) {
            return [];
        }

        $errorMessages = [];

        foreach ($this->constantArgByMethodByType as $type => $positionsByMethods) {
            $positions = $this->positionMatcher->matchPositions(
                $node,
                $scope,
                $type,
                $positionsByMethods,
                $methodCallName
            );
            if ($positions === null) {
                continue;
            }

            foreach ($node->args as $key => $arg) {
                if ($this->shouldSkipArg($key, $positions, $arg)) {
                    continue;
                }

                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $key);
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
    public function run(SomeClass $someClass)
    {
        $someClass->call('name');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class AnotherClass
{
    private OPTION_NAME = 'name';

    public function run(SomeClass $someClass)
    {
        $someClass->call(self::OPTION_NAME);
    }
}
CODE_SAMPLE
                ,
                [
                    'constantArgByMethodByType' => [
                        'SomeClass' => [
                            'call' => [0],
                        ],
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

        if ($arg->value instanceof Variable) {
            return true;
        }

        return $arg->value instanceof ClassConstFetch;
    }
}
