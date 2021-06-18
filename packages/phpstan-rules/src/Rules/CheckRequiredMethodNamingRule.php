<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\CheckRequiredMethodNamingRuleTest
 */
final class CheckRequiredMethodNamingRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Autowired/inject method name must respect "autowire/inject" + class name';

    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@(required|inject)\n?#';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->hasRequiredMarker($node)) {
            return [];
        }

        if ($this->hasRequiredName((string) $node->name, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    /**
     * @required
     */
    public function autowireRandom(...)
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    /**
     * @required
     */
    public function autowireSomeClass(...)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasRequiredName(string $methodName, Scope $scope): bool
    {
        $shortClassName = $this->simpleNameResolver->resolveShortNameFromScope($scope);
        if ($shortClassName === null) {
            return true;
        }

        $requiredMethodNames = ['autowire' . $shortClassName, 'inject' . $shortClassName];
        return in_array($methodName, $requiredMethodNames, true);
    }

    private function hasRequiredMarker(ClassMethod $classMethod): bool
    {
        $docComment = $classMethod->getDocComment();
        if ($docComment instanceof Doc) {
            return (bool) Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX);
        }

        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $attributeName = $this->simpleNameResolver->getName($attribute->name);
                return in_array($attributeName, [Required::class, 'Nette\DI\Attributes\Inject'], true);
            }
        }

        return false;
    }
}
