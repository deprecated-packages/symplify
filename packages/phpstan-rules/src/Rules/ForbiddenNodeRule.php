<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\ErrorSuppress;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @template T as Node
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule\ForbiddenNodeRuleTest
 */
final class ForbiddenNodeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" is forbidden to use';

    /**
     * @var class-string<T>[]
     */
    private $forbiddenNodes = [];

    /**
     * @var Standard
     */
    private $standard;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @param class-string<T>[] $forbiddenNodes
     */
    public function __construct(Standard $standard, ParentNodeFinder $parentNodeFinder, array $forbiddenNodes = [])
    {
        Assert::allIsAOf($forbiddenNodes, Node::class);

        $this->forbiddenNodes = $forbiddenNodes;
        $this->standard = $standard;
        $this->parentNodeFinder = $parentNodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Node::class];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->forbiddenNodes as $forbiddenNode) {
            if (! is_a($node, $forbiddenNode, true)) {
                continue;
            }

            if ($this->hasIntentionallyDocComment($node)) {
                continue;
            }

            $name = $this->resolveNameFromNode($node);
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

    private function resolveNameFromNode(Node $node): string
    {
        return $this->standard->prettyPrint([$node]);
    }

    private function hasIntentionallyDocComment(Node $node): bool
    {
        if (! $node instanceof Stmt) {
            $node = $this->parentNodeFinder->getFirstParentByType($node, Stmt::class);
        }

        if (! $node instanceof Stmt) {
            return false;
        }

        foreach ($node->getComments() as $comment) {
            if (Strings::contains($comment->getText(), 'intention')) {
                return true;
            }
        }

        return false;
    }
}
