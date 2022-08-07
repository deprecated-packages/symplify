<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\NoDuplicatedShortClassNameRuleTest
 */
final class NoDuplicatedShortClassNameRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
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
     * @var array<string, string[]>
     */
    private array $declaredClassesByShortName = [];

    public function __construct(
        private int $toleratedNestingLevel
    ) {
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
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        $className = $classReflection->getName();
        if ($this->isAllowedClass($className)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike->name instanceof Identifier) {
            return [];
        }

        $shortClassName = $classLike->name->toString();

        // make sure classes are unique
        $existingClassesByShortClassName = $this->resolveExistingClassesByShortClassName($shortClassName, $className);
        $this->declaredClassesByShortName[$shortClassName] = $existingClassesByShortClassName;

        $classes = $this->declaredClassesByShortName[$shortClassName] ?? [];
        if (count($classes) <= 1) {
            return [];
        }

        // is nesting level tolerated? - e.g. in case of monorepo project, it's ok to have duplicated classes in 2 levels, e.g. Symplify\\CodingStandard\\
        $classesByToleratedNamespace = [];

        $classesByShortNameCount = count($classes);

        foreach ($classes as $class) {
            $toleratedNamespace = Strings::before($class, '\\', $this->toleratedNestingLevel);
            $classesByToleratedNamespace[$toleratedNamespace][] = $class;
        }

        $toleratedNamespaces = array_keys($classesByToleratedNamespace);

        // this namespace has many classes tolerated → skip it
        if (count($toleratedNamespaces) >= $classesByShortNameCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $shortClassName, implode('", "', $classes));
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
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
                ,
                [
                    'toleratedNestingLevel' => 1,
                ]
            ),
        ]);
    }

    private function isAllowedClass(string $name): bool
    {
        // is allowed
        foreach (self::ALLOWED_CLASS_NAMES as $allowedClassName) {
            if (! Strings::match($name, $allowedClassName)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function resolveExistingClassesByShortClassName(string $shortClassName, string $className): array
    {
        $existingClassesByShortClassName = $this->declaredClassesByShortName[$shortClassName] ?? [];
        $existingClassesByShortClassName[] = $className;

        return array_unique($existingClassesByShortClassName);
    }
}
