<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\ClassConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule\RequireConstantInAttributeArgumentRuleTest
 */
final class RequireConstantInAttributeArgumentRule implements ConfigurableRuleInterface, Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Argument "%s" must be a constant';

    /**
     * @param array<string, string[]> $attributeWithNames
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private AttributeFinder $attributeFinder,
        private array $attributeWithNames
    ) {
    }

    public function getNodeType(): string
    {
        return Node\AttributeGroup::class;
    }

    /**
     * @param Node\AttributeGroup $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        $attributes = $this->attributeFinder->findInClass($node);

        foreach ($attributes as $attribute) {
            $errorMessage = $this->processAttribute($attribute);
            if ($errorMessage === null) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message($errorMessage)
                ->line($attribute->getLine())
                ->build();
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
