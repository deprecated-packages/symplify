<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoDuplicatedShortClassNameRule\NoDuplicatedShortClassNameRuleTest
 */
final class NoDuplicatedShortClassNameRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize';

    /**
     * @var string[][]
     */
    private $declaredClassesByShortName = [];

    /**
     * @var string[]
     */
    private $ALLOWED_CLASS_NAMES = [
        '#File$#',
        # per monorepo package unique
        '#Exception$#',
        '#Option#',
        '#InitCommand#',
        # extended 3rd party class
        '#CommentedOutCodeSniff$#',
        # tests
        '#Some#',
        '#GithubApi#',
    ];

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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

    private function prepareDeclaredClassesByShortName(): void
    {
        // is defined?
        if ($this->declaredClassesByShortName !== []) {
            return;
        }

        foreach (get_declared_classes() as $fullyQualifiedClassName) {
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
        foreach ($this->ALLOWED_CLASS_NAMES as $allowedClassName) {
            if (Strings::match($name, $allowedClassName)) {
                return true;
            }
        }

        return false;
    }
}
