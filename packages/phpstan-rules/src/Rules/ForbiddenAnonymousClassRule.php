<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAnonymousClassRule\ForbiddenAnonymousClassRuleTest
 */
final class ForbiddenAnonymousClassRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Anonymous class is not allowed.';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $shortClassName = $node->name;
        $class = (string) $shortClassName;
        if (Strings::match($class, SimpleNameResolver::ANONYMOUS_CLASS_REGEX)) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
new class {};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{

}

new SomeClass;
CODE_SAMPLE
            ),
        ]);
    }
}
