<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Nette\NetteInjectAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\NoInjectOnFinalRuleTest
 * @implements Rule<InClassNode>
 */
final class NoInjectOnFinalRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use constructor on final classes, instead of property injection';

    public function __construct(
        private NetteInjectAnalyzer $netteInjectAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

final class SomePresenter
{
     #[Inject]
    public $property;
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Nette\DI\Attributes\Inject;

abstract class SomePresenter
{
    #[Inject]
    public $property;
}
CODE_SAMPLE
                ), ]
        );
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if (! $classLike->isFinal()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $errorMessage = [];

        foreach ($classLike->getProperties() as $property) {
            if (! $this->netteInjectAnalyzer->isInjectProperty($property)) {
                continue;
            }

            $errorMessage[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($property->getLine())
                ->build();
        }

        return $errorMessage;
    }
}
