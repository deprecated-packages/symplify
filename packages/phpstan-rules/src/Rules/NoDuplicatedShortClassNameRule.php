<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\NoDuplicatedShortClassNameRuleTest
 */
final class NoDuplicatedShortClassNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASS_NAMES = [
        '#File$#',
        # per monorepo package unique
        '#Exception$#',
        '#Option#',
        '#InitCommand#',
        # tests
        '#Some#',
        '#GithubApi#',
    ];

    /**
     * @var string[][]
     */
    private $declaredClassesByShortName = [];

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $fullyQualifiedClassName = (string) $node->namespacedName;
        if ($fullyQualifiedClassName === '') {
            return [];
        }

        if ($this->isAllowedClass($fullyQualifiedClassName)) {
            return [];
        }

        $this->prepareDeclaredClassesByShortName();

        /** @var string $shortClassName */
        $shortClassName = Strings::after($fullyQualifiedClassName, '\\', -1);

        $classesByShortName = $this->declaredClassesByShortName[$shortClassName] ?? [];
        if (count($classesByShortName) <= 1) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $shortClassName, implode('", "', $classesByShortName));
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
namespace App;

class SomeClass
{
}

namespace App\Nested;

class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App;

class SomeClass
{
}

namespace App\Nested;

class AnotherClass
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function prepareDeclaredClassesByShortName(): void
    {
        // is defined?
        if ($this->declaredClassesByShortName !== []) {
            return;
        }

        $fullyQualifiedClassNames = get_declared_classes();
        foreach ($fullyQualifiedClassNames as $fullyQualifiedClassName) {
            if (! Strings::contains($fullyQualifiedClassName, '\\')) {
                continue;
            }

            $shortClassName = Strings::after($fullyQualifiedClassName, '\\', -1);

            $this->declaredClassesByShortName[$shortClassName][] = $fullyQualifiedClassName;
        }

        ksort($this->declaredClassesByShortName);
    }

    private function isAllowedClass(string $name): bool
    {
        // is allowed
        foreach (self::ALLOWED_CLASS_NAMES as $allowedClassName) {
            if (Strings::match($name, $allowedClassName)) {
                return true;
            }
        }

        return false;
    }
}
