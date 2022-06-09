<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ErrorSuppress;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule\ForbiddenNodeRuleTest
 * @implements Rule<Node>
 */
final class ForbiddenNodeRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" is forbidden to use';

    /**
     * @var array<class-string<\PhpParser\Node>>
     */
    private array $forbiddenNodes = [];

    /**
     * @param array<class-string<\PhpParser\Node>> $forbiddenNodes
     */
    public function __construct(
        private Standard $standard,
        array $forbiddenNodes
    ) {
        Assert::allIsAOf($forbiddenNodes, Node::class);

        $this->forbiddenNodes = $forbiddenNodes;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($this->forbiddenNodes as $forbiddenNode) {
            if (! is_a($node, $forbiddenNode, true)) {
                continue;
            }

            $name = $this->standard->prettyPrint([$node]);
            $errorMessage = sprintf(self::ERROR_MESSAGE, $name);

            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
return @strlen('...');
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
return strlen('...');
CODE_SAMPLE
                ,
                [
                    'forbiddenNodes' => [ErrorSuppress::class],
                ]
            ),
        ]);
    }
}
