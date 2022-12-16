<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\DeadCode;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @deprecated
 */
final class UnusedPublicStaticPropertyRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Public property "$%s" is never used outside of its class';

    /**
     * @var string
     */
    public const TIP_MESSAGE = 'Either reduce the property visibility or annotate it or its class with @api.';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [
            RuleErrorBuilder::message(sprintf(
                'The "%s" rule was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
                self::class,
                'https://github.com/TomasVotruba/unused-public'
            ))->build(),
        ];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class ResultProvider
{
    public static $some;

    public function getSome()
    {
        return self::$some;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class ResultProvider
{
    private static $some;

    public function getSome()
    {
        return self::$some;
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
