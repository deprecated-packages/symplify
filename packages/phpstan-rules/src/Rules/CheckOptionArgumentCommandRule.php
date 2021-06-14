<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symfony\Component\Console\Command\Command;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallArgValueResolver;
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
    public const ERROR_MESSAGE = 'Argument and options "%s" got confused';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private MethodCallArgValueResolver $methodCallArgValueResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->isAbstract()) {
            return [];
        }

        if (! $this->isCommand($node)) {
            return [];
        }

        $extraArgumentNames = $this->resolveNotMatchingMethodCallUsages($node, $scope, 'addArgument', 'getArgument');
        $extraOptionNames = $this->resolveNotMatchingMethodCallUsages($node, $scope, 'addOption', 'getOption');

        if ($extraArgumentNames === [] && $extraOptionNames === []) {
            return [];
        }

        $incorrectNames = array_merge($extraOptionNames, $extraArgumentNames);
        $incorrectNamesString = implode('", "', $incorrectNames);

        $errorMessage = sprintf(self::ERROR_MESSAGE, $incorrectNamesString);
        return [$errorMessage];
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
        $this->addOption('source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addArgument('source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isCommand(Class_ $class): bool
    {
        $className = $this->simpleNameResolver->getName($class);
        if ($className === null) {
            return false;
        }

        return is_a($className, Command::class, true);
    }

    /**
     * @return string[]
     */
    private function resolveNotMatchingMethodCallUsages(
        Class_ $class,
        Scope $scope,
        string $setMethodName,
        string $getMethodName
    ): array {
        $allowedValues = $this->methodCallArgValueResolver->resolveFirstArgInMethodCalls(
            $class,
            $scope,
            $setMethodName
        );

        $usedValues = $this->methodCallArgValueResolver->resolveFirstArgInMethodCalls($class, $scope, $getMethodName);

        return array_diff($usedValues, $allowedValues);
    }
}
