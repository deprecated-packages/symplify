<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Routing\Annotation\Route;
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
        private array $attributeWithNames
    ) {
    }

    public function getNodeType(): string
    {
        return AttributeGroup::class;
    }

    /**
     * @param AttributeGroup $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        foreach ($node->attrs as $attribute) {
            $attributeClassName = $attribute->name->toString();

            $checkedNames = $this->attributeWithNames[$attributeClassName] ?? null;
            if ($checkedNames === null) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                if (! $this->isDesiredArgumentNameWithoutClassConst($arg, $checkedNames)) {
                    continue;
                }

                $argumentName = $arg->name->toString();

                $errorMessage = sprintf(self::ERROR_MESSAGE, $argumentName);
                $errorMessages[] = RuleErrorBuilder::message($errorMessage)
                    ->line($attribute->getLine())
                    ->build();
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

    /**
     * @param string[] $checkedNames
     */
    private function isDesiredArgumentNameWithoutClassConst(Arg $arg, array $checkedNames): bool
    {
        if (! $arg->name instanceof Identifier) {
            return false;
        }

        $argumentName = $arg->name->toString();
        if (! in_array($argumentName, $checkedNames, true)) {
            return false;
        }

        return ! $arg->value instanceof ClassConstFetch;
    }
}
