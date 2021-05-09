<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use Symfony\Component\Finder\Finder;
use Symplify\PHPStanRules\TypeAnalyzer\ContainsTypeAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnNewRule\ForbiddenMethodCallOnNewRuleTest
 */
final class ForbiddenMethodCallOnNewRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call on new expression is not allowed.';

    /**
     * @var array<class-string<Finder>>
     */
    private const ALLOWED_TYPES = ['Symfony\Component\Finder\Finder', 'DateTime', 'Nette\Utils\DateTime'];

    /**
     * @var ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;

    public function __construct(ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->containsTypeAnalyser = $containsTypeAnalyser;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->isMethodCallOnNew($node, $scope)) {
            return [self::ERROR_MESSAGE];
        }

        if ($this->isStaticCallOnNew($node)) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
(new SomeClass())->run();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$someClass = new SomeClass();
$someClass->run();
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param MethodCall|StaticCall $node
     */
    private function isMethodCallOnNew(Node $node, Scope $scope): bool
    {
        if (! $node instanceof MethodCall) {
            return false;
        }

        if (! $node->var instanceof New_) {
            return false;
        }

        return ! $this->containsTypeAnalyser->containsExprTypes($node->var, $scope, self::ALLOWED_TYPES);
    }

    /**
     * @param MethodCall|StaticCall $node
     */
    private function isStaticCallOnNew(Node $node): bool
    {
        if (! $node instanceof StaticCall) {
            return false;
        }

        return $node->class instanceof New_;
    }
}
