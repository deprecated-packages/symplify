<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Composer\ClassNamespaceMatcher;
use Symplify\PHPStanRules\Composer\ComposerAutoloadResolver;
use Symplify\PHPStanRules\Composer\Psr4PathValidator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule\CheckClassNamespaceFollowPsr4RuleTest
 */
final class CheckClassNamespaceFollowPsr4Rule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class like namespace "%s" does not follow PSR-4 configuration in composer.json';

    /**
     * @var array<string, string|string[]>
     */
    private $autoloadPsr4Paths = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        ComposerAutoloadResolver $composerAutoloadResolver,
        private Psr4PathValidator $psr4PathValidator,
        private ClassNamespaceMatcher $classNamespaceMatcher
    ) {
        $this->autoloadPsr4Paths = $composerAutoloadResolver->getPsr4Autoload();
    }

    /**
     * @return array<class-string<Node>>
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
        if ($this->autoloadPsr4Paths === []) {
            return [];
        }

        $namespaceBeforeClass = $this->resolveNamespaceBeforeClass($node);
        if ($namespaceBeforeClass === null) {
            return [];
        }

        $filePath = str_replace('\\', '/', $scope->getFile());

        $possibleNamespacesToDirectories = $this->classNamespaceMatcher->matchPossibleDirectoriesForClass(
            $namespaceBeforeClass,
            $this->autoloadPsr4Paths,
            $scope
        );

        if ($possibleNamespacesToDirectories === []) {
            return [];
        }

        foreach ($possibleNamespacesToDirectories as $possibleNamespaceToDirectory) {
            if ($this->psr4PathValidator->isClassNamespaceCorrect($possibleNamespaceToDirectory, $filePath)) {
                return [];
            }
        }

        $namespacePart = substr($namespaceBeforeClass, 0, -1);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $namespacePart);

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo;

class Baz
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo\Bar;

class Baz
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveNamespacePartOfClass(string $className, string $shortClassName): string
    {
        return Strings::substring($className, 0, -strlen($shortClassName));
    }

    private function resolveNamespaceBeforeClass(ClassLike $classLike): ?string
    {
        $className = $this->simpleNameResolver->getName($classLike);
        if ($className === null) {
            return null;
        }

        $shortClassName = $this->simpleNameResolver->resolveShortNameFromNode($classLike);
        if ($shortClassName === null) {
            return null;
        }

        return $this->resolveNamespacePartOfClass($className, $shortClassName);
    }
}
