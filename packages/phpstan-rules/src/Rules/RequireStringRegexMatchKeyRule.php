<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use Symplify\Astral\Reflection\ReflectionParser;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireStringRegexMatchKeyRule\RequireStringRegexMatchKeyRuleTest
 */
final class RequireStringRegexMatchKeyRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Regex must use string named capture groups instead of numeric';

    public function __construct(
        private NodeFinder $nodeFinder,
        private ReflectionParser $reflectionParser,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipExpr($node->expr)) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        $functionReflection = $scope->getFunction();
        if (! $functionReflection instanceof MethodReflection) {
            return [];
        }

        $classMethod = $this->reflectionParser->parseMethodReflection($functionReflection);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        $usedAsArrayDimFetches = $this->findVariableArrayDimFetches($classMethod, $node->var);
        if ($usedAsArrayDimFetches === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches[1];
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(?<content>a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches['content'];
        }
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return ArrayDimFetch[]
     */
    private function findVariableArrayDimFetches(ClassMethod $classMethod, Variable $variable): array
    {
        if (! is_string($variable->name)) {
            return [];
        }

        $variableName = $variable->name;

        return $this->nodeFinder->find($classMethod, static function (Node $node) use ($variableName): bool {
            if (! $node instanceof ArrayDimFetch) {
                return false;
            }

            if (! $node->var instanceof Variable) {
                return false;
            }

            if (! $node->dim instanceof LNumber) {
                return false;
            }

            if (! is_string($node->var->name)) {
                return false;
            }

            return $node->var->name === $variableName;
        });
    }

    private function shouldSkipExpr(Expr $expr): bool
    {
        if (! $expr instanceof StaticCall) {
            return true;
        }

        if (! $expr->class instanceof Name) {
            return true;
        }

        if ($expr->class->toString() !== 'Nette\Utils\Strings') {
            return true;
        }

        if (! $expr->name instanceof Identifier) {
            return true;
        }

        return $expr->name->toString() !== 'match';
    }
}
