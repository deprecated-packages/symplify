<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule\NoMaskWithoutSprintfRuleTest
 */
final class NoMaskWithoutSprintfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Missing sprintf() function for a mask';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /**
     * @param String_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $stringKind = $node->getAttribute(AttributeKey::KIND);
        if (in_array($stringKind, [String_::KIND_NOWDOC, String_::KIND_HEREDOC], true)) {
            return [];
        }
        if (! str_contains($node->value, '%s')) {
            return [];
        }
        if ($node->value === '%s') {
            return [];
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent === null) {
            return [];
        }

        if ($this->shouldSkipParentType($parent)) {
            return [];
        }

        if (! $parent instanceof Arg) {
            return [self::ERROR_MESSAGE];
        }

        $parentParent = $parent->getAttribute(AttributeKey::PARENT);
        if (! $parentParent instanceof FuncCall) {
            return [self::ERROR_MESSAGE];
        }

        if ($this->simpleNameResolver->isName($parentParent, 'sprintf')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
return 'Hey %s';
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
return sprintf('Hey %s', 'Matthias');
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipParentType(Node $node): bool
    {
        if ($node instanceof Const_) {
            return true;
        }

        if ($node instanceof BinaryOp) {
            return true;
        }

        return $node instanceof ArrayItem;
    }
}
