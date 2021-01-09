<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symfony\Component\Console\Command\Command;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeFinder\ParentNodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\CheckOptionArgumentCommandRuleTest
 */
final class CheckOptionArgumentCommandRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s() called in configure(), must be called with %s() in execute() in "Symfony\Component\Console\Command\Command" type';

    /**
     * @var array<string, string>
     */
    private const METHOD_CALL_NOTMATCH = [
        'addOption' => 'getArgument',
        'addArgument' => 'getOption',
    ];

    /**
     * @var array<string, string>
     */
    private const METHOD_CALL_MATCH = [
        'addOption' => 'getOption',
        'addArgument' => 'getArgument',
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    public function __construct(
        NodeFinder $nodeFinder,
        NodeComparator $nodeComparator,
        SimpleNameResolver $simpleNameResolver,
        ParentNodeFinder $parentNodeFinder
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
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
        if (! $this->isInInstanceOfCommand($node, $scope)) {
            return [];
        }

        if (! $this->isInConfigureMethod($node)) {
            return [];
        }

        $methodCallName = $this->simpleNameResolver->getName($node->name);
        if ($methodCallName === null) {
            return [];
        }

        if (! array_key_exists($methodCallName, self::METHOD_CALL_MATCH)) {
            return [];
        }

        return $this->validateInvalidMethodCall($node, $methodCallName);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getArgument(Option::CATEGORIZE);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getOption(Option::CATEGORIZE);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function validateInvalidMethodCall(MethodCall $methodCall, string $methodCallName): array
    {
        $class = $this->parentNodeFinder->getFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        /** @var ClassMethod|null $executeClassMethod */
        $executeClassMethod = $this->getExecuteClassMethod($class);
        if ($executeClassMethod === null) {
            return [];
        }

        $passedArg = $methodCall->args[0]->value;
        $invalidMethodCall = self::METHOD_CALL_NOTMATCH[$methodCallName];

        $isFoundInvalidMethodCall = $this->isInvalidMethodCallFound(
            $executeClassMethod,
            $passedArg,
            $invalidMethodCall
        );
        if ($isFoundInvalidMethodCall) {
            return [sprintf(self::ERROR_MESSAGE, $methodCallName, self::METHOD_CALL_MATCH[$methodCallName])];
        }

        return [];
    }

    private function getExecuteClassMethod(Class_ $class): ?Node
    {
        return $this->nodeFinder->findFirst($class, function (Node $node): bool {
            if (! $node instanceof ClassMethod) {
                return false;
            }

            return $this->simpleNameResolver->isName($node, 'execute');
        });
    }

    private function isInConfigureMethod(MethodCall $methodCall): bool
    {
        $classMethod = $this->parentNodeFinder->getFirstParentByType($methodCall, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        return $this->simpleNameResolver->isName($classMethod, 'configure');
    }

    private function isInInstanceOfCommand(MethodCall $methodCall, Scope $scope): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return false;
        }

        $callerType = $scope->getType($methodCall->var);
        if (! $callerType instanceof ThisType) {
            return false;
        }

        return is_a($className, Command::class, true);
    }

    private function isInvalidMethodCallFound(
        ClassMethod $executeClassMethod,
        Expr $argExpr,
        string $invalidMethodCall
    ): bool {
        return (bool) $this->nodeFinder->findFirst(
            (array) $executeClassMethod->stmts,
            function (Node $node) use ($argExpr, $invalidMethodCall, $executeClassMethod): bool {
                if (! $node instanceof MethodCall) {
                    return false;
                }

                if (! $this->simpleNameResolver->isName($node->name, $invalidMethodCall)) {
                    return false;
                }

                $params = $executeClassMethod->getParams();
                if ($params === []) {
                    return false;
                }

                if (! $this->nodeComparator->areNodesEqual($params[0]->var, $node->var)) {
                    return false;
                }

                return $this->nodeComparator->areNodesEqual($node->args[0]->value, $argExpr);
            }
        );
    }
}
