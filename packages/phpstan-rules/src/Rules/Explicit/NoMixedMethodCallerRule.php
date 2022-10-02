<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ErrorType;
use PHPStan\Type\MixedType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\NoMixedMethodCallerRuleTest
 */
final class NoMixedMethodCallerRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Anonymous variable in a `%s->...()` method call can lead to false dead methods. Make sure the variable type is known';

    public function __construct(
        private Standard $printerStandard,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof MixedType) {
            return [];
        }

        if ($callerType instanceof ErrorType) {
            return [];
        }

        $printedMethodCall = $this->printerStandard->prettyPrintExpr($node->var);

        return [sprintf(self::ERROR_MESSAGE, $printedMethodCall)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
function run($unknownType)
{
    return $unknownType->call();
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function run(KnownType $knownType)
{
    return $knownType->call();
}
CODE_SAMPLE
            ),
        ]);
    }
}
