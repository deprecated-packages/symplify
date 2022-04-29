<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Json;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Finder\ClassLikeNameFinder;
use Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\ClassExtendingExclusiveNamespaceRuleTest
 */
final class ClassExtendingExclusiveNamespaceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is authorized to exist in one of the following namespaces: %s, but it is in namespace "%s". Please move it to one of the authorized namespaces.';

    /**
     * @param array<string, array<string>> $guards
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ReflectionProvider $reflectionProvider,
        private ClassLikeNameMatcher $classLikeNameMatcher,
        private ClassLikeNameFinder $classLikeNameFinder,
        private array $guards
    ) {
    }

    /**
     * @return class-string<Node>[]
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
        $classLikeName = $this->simpleNameResolver->getName($node);
        if ($classLikeName === null) {
            return [];
        }

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($classLikeName);
        foreach ($this->guards as $guardedTypeOrNamespacePattern => $allowedNamespacePatterns) {
            if (! $this->isSubjectToGuardedTypeOrNamespacePattern($classReflection, $guardedTypeOrNamespacePattern)) {
                continue;
            }

            if (! $this->isInAllowedNamespace($allowedNamespacePatterns, $classLikeName)) {
                $nativeReflectionClass = $classReflection->getNativeReflection();
                $errorMessage = sprintf(
                    self::ERROR_MESSAGE,
                    $classLikeName,
                    Json::encode($allowedNamespacePatterns, JSON_THROW_ON_ERROR),
                    $nativeReflectionClass->getNamespaceName(),
                );

                return [$errorMessage];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Define in which namespaces (using *, ** or ? glob-like pattern matching) can classes extending specified class or implementing specified interface exist', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App;

// AbstractType implements \Symfony\Component\Form\FormTypeInterface
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
CODE_SAMPLE
                ,
                [
                    'guards' => [
                        'Symfony\Component\Form\FormTypeInterface' => ['App\Form\**'],
                    ],
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App\Services;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Component\PriceEngineImpl;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
CODE_SAMPLE
                ,
                [
                    'guards' => [
                        'App\Component\PriceEngine\**' => [
                            'App\Component\PriceEngine\**',
                            'App\Component\PriceEngineImpl\**',
                        ],
                    ],
                ]
            ),
        ]);
    }

    private function isSubjectToGuardedTypeOrNamespacePattern(
        ClassReflection $classReflection,
        string $guardedTypeOrNamespacePattern,
    ): bool {
        $isGuardedSubjectNamespacePattern = str_contains($guardedTypeOrNamespacePattern, '*') || str_contains(
            $guardedTypeOrNamespacePattern,
            '?'
        );
        $isGuardedSubjectType = ! $isGuardedSubjectNamespacePattern;

        if ($isGuardedSubjectType && ! $classReflection->isSubclassOf($guardedTypeOrNamespacePattern)) {
            return false;
        }

        if (! $isGuardedSubjectNamespacePattern) {
            return true;
        }

        return $this->isSubjectSubclassOfGuardedPattern($guardedTypeOrNamespacePattern, $classReflection);
    }

    private function isSubjectSubclassOfGuardedPattern(
        string $guardedTypeOrNamespacePattern,
        ClassReflection $classReflection
    ): bool {
        $classLikeNames = $this->classLikeNameFinder->getClassLikeNamesMatchingNamespacePattern(
            $guardedTypeOrNamespacePattern
        );
        foreach ($classLikeNames as $classLikeName) {
            if ($classReflection->isSubclassOf($classLikeName)) {
                return true;
            }
        }

        return false;
    }

    private function isInAllowedNamespace(mixed $allowedNamespacePatterns, string $classLikeName): bool
    {
        foreach ($allowedNamespacePatterns as $allowedNamespacePattern) {
            if ($this->classLikeNameMatcher->isClassLikeNameMatchedAgainstPattern(
                $classLikeName,
                $allowedNamespacePattern
            )) {
                return true;
            }
        }

        return false;
    }
}
