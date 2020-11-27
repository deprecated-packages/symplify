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
    private const METHOD_CALL_INVALID = [
        'addoption' => 'getArgument',
        'addargument' => 'getOption',
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
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
        if (! in_array(strtolower($methodCallName), ['addoption', 'addargument'], true)) {
            return [];
        }

        $class = $this->resolveCurrentClass($node);
        if (! $class instanceof Class_) {
            return [];
        }

        $executeClassMethod = $this->nodeFinder->find($class, function (Node $node): bool {
            return $node instanceof ClassMethod && $node->name instanceof Identifier && $node->name->toString() === 'execute';
        });

        return [];
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
        return strtolower($methodName) === 'configure';
    }

    private function isInInstanceOfCommand(MethodCall $methodCall, Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return false;
        }

        return $scope->getType($methodCall) instanceof ThisType && is_a($className, Command::class, true);
    }
}
