<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanRules\PhpDoc\ClassAnnotationResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule\PreferredAttributeOverAnnotationRuleTest
 */
final class PreferredAttributeOverAnnotationRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use attribute instead of "%s" annotation';

    /**
     * @var string[]
     */
    private $annotations = [];

    /**
     * @var ClassAnnotationResolver
     */
    private $classAnnotationResolver;

    /**
     * @param string[] $annotations
     */
    public function __construct(ClassAnnotationResolver $classAnnotationResolver, array $annotations)
    {
        $this->annotations = $annotations;
        $this->classAnnotationResolver = $classAnnotationResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Property::class, Class_::class];
    }

    /**
     * @param ClassMethod|Property|Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classAnnotations = $this->classAnnotationResolver->resolveClassAnnotations($node, $scope);
        if ($classAnnotations === []) {
            return [];
        }

        $matchedAnnotations = array_intersect($classAnnotations, $this->annotations);

        $errorsMessages = [];
        foreach ($matchedAnnotations as $matchedAnnotation) {
            $errorsMessages[] = sprintf(self::ERROR_MESSAGE, $matchedAnnotation);
        }

        return $errorsMessages;
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
    #Route()
    public function action()
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'annotations' => [Route::class],
                ]
            ),
        ]);
    }
}
