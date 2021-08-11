<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooLongFunctionLikeRule\TooLongFunctionLikeRuleTest
 *
 * @deprecated This rule is rather academic and does not relate ot complexity. Use
 * @see ClassLikeCognitiveComplexityRule instead
 */
final class TooLongFunctionLikeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s has %d lines, it is too long. Shorted it under %d lines';

    public function __construct(
        private int $maxFunctionLikeLength = 20
    ) {
        if (! StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $errorMessage = sprintf(
                '[Deprecated] This rule is rather academic and does not relate ot complexity. Use
 "%s" instead',
                ClassLikeCognitiveComplexityRule::class
            );
            trigger_error($errorMessage);
            sleep(3);
        }
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Function_::class, ClassMethod::class];
    }

    /**
     * @param Function_|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentFunctionLikeLength = $this->getNodeLength($node);
        if ($currentFunctionLikeLength <= $this->maxFunctionLikeLength) {
            return [];
        }

        $functionLikeType = $this->resolveFunctionLikeType($node);

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $functionLikeType,
            $currentFunctionLikeLength,
            $this->maxFunctionLikeLength
        );

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
function some()
{
    if (...) {
        return 1;
    } else {
        return 2;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function some()
{
    return (...) ? 1 : 2;
}
CODE_SAMPLE
                ,
                [
                    'maxFunctionLikeLength' => 3,
                ]
            ),
        ]);
    }

    private function resolveFunctionLikeType(Function_ | ClassMethod $functionLike): string
    {
        if ($functionLike instanceof Function_) {
            return 'Function';
        }

        return 'Method';
    }

    private function getNodeLength(Node $node): int
    {
        return $node->getEndLine() - $node->getStartLine();
    }
}
