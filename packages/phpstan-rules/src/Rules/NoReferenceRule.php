<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 */
final class NoReferenceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit return value over magic &reference';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    public function __construct(ParentMethodAnalyser $parentMethodAnalyser)
    {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
            Function_::class,
            AssignRef::class,
            Arg::class,
            Foreach_::class,
            ArrayItem::class,
            ArrowFunction::class,
            Closure::class,
        ];
    }

    /**
     * @param ClassMethod|Function_|AssignRef|Arg|Foreach_|ArrayItem|ArrowFunction|Closure $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        if ($node instanceof AssignRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        } elseif ($node->byRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        }

        $paramErrorMessage = $this->collectParamErrorMessages($node, $scope);
        $errorMessages = array_merge($errorMessages, $paramErrorMessage);

        return array_unique($errorMessages);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(&$value)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run($value)
    {
        return $value;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function collectParamErrorMessages(Node $node, Scope $scope): array
    {
        if (! $node instanceof Function_ && ! $node instanceof ClassMethod) {
            return [];
        }

        // has parent method? → skip it as enforced by parent
        $methodName = (string) $node->name;
        if ($this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        $errorMessages = [];
        foreach ($node->params as $param) {
            /** @var Param $param */
            if (! $param->byRef) {
                continue;
            }

            $errorMessages[] = self::ERROR_MESSAGE;
        }

        return $errorMessages;
    }
}
