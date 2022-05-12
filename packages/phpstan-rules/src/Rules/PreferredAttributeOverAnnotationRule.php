<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\PhpDoc\ClassAnnotationResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule\PreferredAttributeOverAnnotationRuleTest
 */
final class PreferredAttributeOverAnnotationRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use attribute instead of "%s" annotation';

    /**
     * @param string[] $annotations
     */
    public function __construct(
        private ClassAnnotationResolver $classAnnotationResolver,
        private array $annotations
    ) {
    }

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

        $targetNodes = array_merge($classLike->getProperties(), $classLike->getMethods(), [$classLike]);

        $ruleErrors = [];

        foreach ($targetNodes as $targetNode) {
            $classAnnotations = $this->classAnnotationResolver->resolveClassAnnotations($targetNode, $scope);
            if ($classAnnotations === []) {
                continue;
            }

            $matchedAnnotations = array_intersect($classAnnotations, $this->annotations);

            foreach ($matchedAnnotations as $matchedAnnotation) {
                $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $matchedAnnotation))
                    ->line($targetNode->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    /**
     * @Route()
     */
    public function action()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route]
    public function action()
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'annotations' => ['Symfony\Component\Routing\Annotation\Route'],
                ]
            ),
        ]);
    }
}
