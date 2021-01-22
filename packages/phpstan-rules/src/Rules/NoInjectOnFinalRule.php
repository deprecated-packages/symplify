<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule\NoInjectOnFinalRuleTest
 */
final class NoInjectOnFinalRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property @inject can be used on non-final class only';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
final class SomePresenter
{
    /**
     * @inject
     */
    public $property;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
abstract class SomePresenter
{
    /**
     * @inject
     */
    public $property;
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        if (! Strings::contains($docComment->getText(), '@inject')) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isFinal()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
