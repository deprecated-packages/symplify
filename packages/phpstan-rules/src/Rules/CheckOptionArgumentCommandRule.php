<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symfony\Component\Console\Command\Command;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ThisType;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\CheckOptionArgumentCommandRuleTest
 */
final class CheckOptionArgumentCommandRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s() called in configure(), must be called with %s() in execute() in Symfony\Component\Console\Command\Command class';

    /**
     * @var string
     */
    private const METHOD_CALL_SUGGESTION = [
        'addOption'    => 'getOption',
        'addArguments' => 'getArguments'
    ];

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
        $className = $this->getClassName($scope);
        if (! is_a($className, Command::class, true)) {
            return [];
        }

        $classMethod = $this->resolveCurrentClassMethod($node);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        $name = (string) $classMethod->name;
        if (strtolower($name) !== 'configure') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if ($callerType instanceof ThisType) {
            return [];
        }

        $methodCallName = (string) $node->name;
        if (! in_array(strtolower($methodCallName), ['addoption', 'addarguments'], true)) {
            return [];
        }

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
}
