<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\ClassNamespaceGuardRuleTest
 */
final class ClassNamespaceGuardRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
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

        foreach ($this->guards as $type => $allowedNamespacePatterns) {
            $classReflection = $this->reflectionProvider->getClass($classLikeName);

            if (! $classReflection->isSubclassOf($type)) {
                continue;
            }

            $isInAllowedNamespace = false;
            foreach ($allowedNamespacePatterns as $allowedNamespacePattern) {
                if ($this->isClassLikeNameMatchedAgainstPattern($classLikeName, $allowedNamespacePattern)) {
                    $isInAllowedNamespace = true;
                    break;
                }
            }

            if (! $isInAllowedNamespace) {
                $errorMessage = sprintf(
                    self::ERROR_MESSAGE,
                    $classLikeName,
                    json_encode($allowedNamespacePatterns, JSON_THROW_ON_ERROR),
                    $classReflection->getNativeReflection()
                        ->getNamespaceName(),
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
namespace App;

use Exception;

class ProductNotFoundException extends Exception
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Exception;

use Exception;

class ProductNotFoundException extends Exception
{
}
CODE_SAMPLE
                ,
                [
                    'guards' => [
                        Throwable::class => ['App\Exception\**', 'App\Services\**'],
                    ],
                ]
            ),
        ]);
    }

    private function isClassLikeNameMatchedAgainstPattern(string $className, string $namespaceWildcardPattern): bool
    {
        $regex = preg_replace_callback(
            '#\*{1,2}|\?|[\\\^$.[\]|():+{}=!<>\-\#]#',
            fn (array $matches): string => match ($matches[0]) {
            '**' => '.*',
            '*' => '[^\\\\]*',
            '?' => '[^\\\\]',
            default => '\\' . $matches[0],
        },
            $namespaceWildcardPattern
        );

        return (bool) preg_match('/^' . $regex . '$/s', $className);
    }
}
