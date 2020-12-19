<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symfony\Component\Console\Command\Command;
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
     * @var string
     */
    private const METHOD_CALL_NOTMATCH = [
        'addOption' => 'getArgument',
        'addArgument' => 'getOption',
    ];

    /**
     * @var string
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

    public function __construct(NodeFinder $nodeFinder, NodeComparator $nodeComparator)
    {
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
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

        $methodCallIdentifier = $node->name;
        if (! $methodCallIdentifier instanceof Identifier) {
            return [];
        }

        $methodCallName = $methodCallIdentifier->toString();
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

    private function validateInvalidMethodCall(MethodCall $methodCall, string $methodCallName): array
    {
        $class = $this->resolveCurrentClass($methodCall);
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

        $isFoundInvalidMethodCall = (bool) $this->nodeFinder->findFirst(
            (array) $executeClassMethod->stmts,
            function (Node $node) use ($passedArg, $invalidMethodCall, $executeClassMethod): bool {
                if (! $node instanceof MethodCall) {
                    return false;
                }

                if (! $node->name instanceof Identifier || $node->name->toString() !== $invalidMethodCall) {
                    return false;
                }

                $params = $executeClassMethod->getParams();
                if ($params === []) {
                    return false;
                }

                if (! $this->nodeComparator->areNodesEqual($params[0]->var, $node->var)) {
                    return false;
                }

                return $this->nodeComparator->areNodesEqual($node->args[0]->value, $passedArg);
            }
        );

        if ($isFoundInvalidMethodCall) {
            return [sprintf(self::ERROR_MESSAGE, $methodCallName, self::METHOD_CALL_MATCH[$methodCallName])];
        }

        return [];
    }

    private function getExecuteClassMethod(Class_ $class): ?Node
    {
        return $this->nodeFinder->findFirst($class, function (Node $node): bool {
            return $node instanceof ClassMethod
                && $node->name instanceof Identifier
                && $node->name->toString() === 'execute';
        });
    }

    private function isInConfigureMethod(MethodCall $methodCall): bool
    {
        $classMethod = $this->resolveCurrentClassMethod($methodCall);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        $methodNameIdentifier = $classMethod->name;
        if (! $methodNameIdentifier instanceof Identifier) {
            return false;
        }

        $methodName = $methodNameIdentifier->toString();
        return $methodName === 'configure';
    }

    private function isInInstanceOfCommand(MethodCall $methodCall, Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return false;
        }

        return $scope->getType($methodCall->var) instanceof ThisType && is_a($className, Command::class, true);
    }
}
