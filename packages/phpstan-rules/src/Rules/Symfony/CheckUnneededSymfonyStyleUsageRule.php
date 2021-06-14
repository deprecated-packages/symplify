<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\ClassMethodsNodeAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\CheckUnneededSymfonyStyleUsageRule\CheckUnneededSymfonyStyleUsageRuleTest
 */
final class CheckUnneededSymfonyStyleUsageRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'SymfonyStyle service is not needed for only newline and text echo. Use PHP_EOL and concatenation instead';

    /**
     * @var string[]
     */
    private const SIMPLE_CONSOLE_OUTPUT_METHODS = ['newline', 'write', 'writeln'];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ClassMethodsNodeAnalyzer $classMethodsNodeAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethodsNode::class];
    }

    /**
     * @param ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var ClassLike $classLike */
        $classLike = $node->getClass();
        if ($this->shouldSkipClass($classLike)) {
            return [];
        }

        $symfonyStyleMethodCalls = $this->classMethodsNodeAnalyzer->resolveMethodCallsByType(
            $node,
            SymfonyStyle::class
        );
        if ($symfonyStyleMethodCalls === []) {
            return [];
        }

        if ($this->hasUsefulSymfonyStyleMethodNames($symfonyStyleMethodCalls)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Console\Style\SymfonyStyle;

class SomeClass
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run()
    {
        $this->symfonyStyle->writeln('Hi');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        echo 'Hi' . PHP_EOL;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClass(ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if ($classLike->extends === null) {
            return false;
        }

        $parentClass = $classLike->extends->toString();
        if (is_a($parentClass, SymfonyStyle::class, true)) {
            return true;
        }

        return is_a($parentClass, Command::class, true);
    }

    /**
     * @param MethodCall[] $methodCalls
     */
    private function hasUsefulSymfonyStyleMethodNames(array $methodCalls): bool
    {
        foreach ($methodCalls as $methodCall) {
            if (! $this->simpleNameResolver->isNames($methodCall->name, self::SIMPLE_CONSOLE_OUTPUT_METHODS)) {
                return true;
            }
        }

        return false;
    }
}
