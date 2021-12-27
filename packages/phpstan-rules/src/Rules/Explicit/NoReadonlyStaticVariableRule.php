<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Static_;
use PhpParser\Node\Stmt\StaticVar;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\NodeAnalyzer\WriteVariableAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule\NoReadonlyStaticVariableRuleTest
 */
final class NoReadonlyStaticVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid using static variables, as they can change. Use class constant instead';

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private WriteVariableAnalyzer $writeVariableAnalyzer,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        static $list = [1, 2, 3];

        return $list;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private const LIST = [1, 2, 3];
    public function run()
    {
        return self::LIST;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Static_[] $statics */
        $statics = $this->simpleNodeFinder->findByType($node, Static_::class);

        $ruleErrors = [];

        foreach ($statics as $static) {
            foreach ($static->vars as $staticVar) {
                $staticVariableName = $this->simpleNameResolver->getName($staticVar->var);
                if ($staticVariableName === null) {
                    continue;
                }

                $nonStaticVariables = $this->findNonStaticVariablesByName($node, $staticVariableName);

                if ($this->hasVariablesWritten($nonStaticVariables)) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->line($staticVar->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    /**
     * @return Variable[]
     */
    private function findNonStaticVariablesByName(ClassMethod $classMethod, string $variableName): array
    {
        /** @var Variable[] $variables */
        $variables = $this->simpleNodeFinder->findByType($classMethod, Variable::class);

        $nonStaticVariables = [];
        foreach ($variables as $variable) {
            if (! $this->simpleNameResolver->isName($variable, $variableName)) {
                continue;
            }

            $parent = $variable->getAttribute(AttributeKey::PARENT);
            if ($parent instanceof StaticVar) {
                continue;
            }

            $nonStaticVariables[] = $variable;
        }

        return $nonStaticVariables;
    }

    /**
     * @param Variable[] $nonStaticVariables
     */
    private function hasVariablesWritten(array $nonStaticVariables): bool
    {
        foreach ($nonStaticVariables as $nonStaticVariable) {
            if ($this->writeVariableAnalyzer->isVariableWritten($nonStaticVariable)) {
                return true;
            }
        }

        return false;
    }
}
