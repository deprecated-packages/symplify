<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\ClassMethodsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\CheckUnneededSymfonyStyleUsageRuleTest
 */
final class CheckUnneededSymfonyStyleUsageRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'SymfonyStyle usage is unneeded for only newline, write, and/or writeln, use PHP_EOL and concatenation instead';

    /**
     * @var string[]
     */
    private const SIMPLE_CONSOLE_OUTPUT_METHODS = ['newline', 'write', 'writeln'];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ClassMethodsNodeAnalyzer
     */
    private $classMethodsNodeAnalyzer;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ClassMethodsNodeAnalyzer $classMethodsNodeAnalyzer
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->classMethodsNodeAnalyzer = $classMethodsNodeAnalyzer;
    }

    /**
     * @return string[]
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
        if ($this->hasParentClassSymfonyStyle($classLike)) {
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
        echo 'Hi'. PHP_EOL;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasParentClassSymfonyStyle(ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if ($classLike->extends === null) {
            return false;
        }

        $parentClass = $classLike->extends->toString();

        return is_a($parentClass, SymfonyStyle::class, true);
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
