<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\RequireConstantInAttributeArgumentRuleTest
 */
final class RequireConstantInAttributeArgumentRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Argument "%s" must be a constant';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var array<string, string[]>
     */
    private $attributeWithNames = [];

    /**
     * @param array<string, string[]> $attributeWithNames
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $attributeWithNames)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->attributeWithNames = $attributeWithNames;
    }

    /**
     * @return string[]
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
        $errorMessages = [];

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $errorMessage = $this->processAttribute($attribute);
                if ($errorMessage === null) {
                    continue;
                }

                $errorMessages[] = $errorMessage;
            }
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

final class SomeClass
{
    #[Route(path: '/archive', name: 'blog_archive')]
    public function __invoke()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

final class SomeClass
{
    #[Route(path: '/archive', name: RouteName::BLOG_ARCHIVE)]
    public function __invoke()
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'attributeWithNames' => [
                        Route::class => ['name'],
                    ],
                ]
            ),
        ]);
    }

    private function processAttribute(Attribute $attribute): ?string
    {
        $attributeClassName = $this->simpleNameResolver->getName($attribute);

        foreach ($this->attributeWithNames as $checkedAttribute => $checkedNames) {
            if ($checkedAttribute !== $attributeClassName) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                $argumentName = $this->simpleNameResolver->getName($arg);
                if ($argumentName === null) {
                    continue;
                }

                if ($this->shouldSkipArgument($checkedNames, $argumentName, $arg)) {
                    continue;
                }

                return sprintf(self::ERROR_MESSAGE, $argumentName);
            }
        }

        return null;
    }

    /**
     * @param string[] $checkedNames
     */
    private function shouldSkipArgument(array $checkedNames, string $argumentName, Arg $arg): bool
    {
        if (! in_array($argumentName, $checkedNames, true)) {
            return true;
        }

        return $arg->value instanceof ClassConstFetch;
    }
}
