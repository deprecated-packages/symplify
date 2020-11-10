<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoTraitExceptRequiredAutowireRule\NoTraitExceptRequiredAutowireRuleTest
 */
final class NoTraitExceptRequiredAutowireRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use trait';

    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@required\n?#';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Trait_::class];
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methods = $node->getMethods();
        if ($methods === []) {
            return [self::ERROR_MESSAGE];
        }

        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            if ($docComment === null) {
                return [self::ERROR_MESSAGE];
            }

            if (! $method->isPublic()) {
                return [self::ERROR_MESSAGE];
            }

            if (! Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
trait SomeTrait
{
    public function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
trait SomeTrait
{
    /**
     * @required
     */
    public function autowire(...)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
