<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ComposerAutoloadResolver;
use Symplify\PHPStanRules\Location\DirectoryChecker;
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
    public const ERROR_MESSAGE = '%s namespace "%s" does not follow PSR-4 configuration in composer.json';

    /**
     * @see https://regex101.com/r/ChpDsj/1
     * @var string
     */
    private const ANONYMOUS_CLASS_REGEX = '#^AnonymousClass[\w+]#';

    /**
     * @var array<string, string>
     */
    private $autoloadPsr4Paths = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var DirectoryChecker
     */
    private $directoryChecker;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ComposerAutoloadResolver $composerAutoloadResolver,
        DirectoryChecker $directoryChecker
    ) {
        $this->autoloadPsr4Paths = $composerAutoloadResolver->getPsr4Autoload();
        $this->simpleNameResolver = $simpleNameResolver;
        $this->directoryChecker = $directoryChecker;
    }

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
        if ($this->autoloadPsr4Paths === []) {
            return [];
        }

        $className = $this->simpleNameResolver->getName($node);
        if ($className === null) {
            return [];
        }

        $shortClassName = (string) $node->name;
        if (Strings::match($shortClassName, self::ANONYMOUS_CLASS_REGEX)) {
            return [];
        }

        $file = str_replace('\\', '/', $scope->getFile());
        $namespaceBeforeClass = $this->resolveNamespacePartOfClass($className, $shortClassName);

        foreach ($this->autoloadPsr4Paths as $namespace => $directory) {
            $namespace = rtrim($namespace, '\\') . '\\';
            if ($namespaceBeforeClass === $namespace) {
                return [];
            }

            $directories = $this->resolveDirectories($directory);
            foreach ($directories as $singleDirectory) {
                if (! $this->directoryChecker->isInDirectoryNamed($scope, $singleDirectory)) {
                    continue;
                }

                if ($this->isClassNamespaceCorrect($namespace, $singleDirectory, $namespaceBeforeClass, $file)) {
                    return [];
                }
            }
        }

        $type = $this->getType($className, $file);

        $namespacePart = substr($namespaceBeforeClass, 0, -1);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $type, $namespacePart);
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

    private function getType(string $namespacedName, string $file): string
    {
        // totally different namespace needs include file
        include_once $file;

        if (trait_exists($namespacedName)) {
            return 'Trait';
        }

        if (interface_exists($namespacedName)) {
            return 'Interface';
        }

        return 'Class';
    }

    private function isClassNamespaceCorrect(
        string $namespace,
        string $directory,
        string $namespaceBeforeClass,
        string $file
    ): bool {
        /** @var array<int, string> $paths */
        $paths = explode($directory, $file);
        if (count($paths) === 1) {
            return false;
        }

        $directoryInNamespacedRoot = dirname($paths[1]);
        $directoryInNamespacedRoot = $this->normalizePath($directoryInNamespacedRoot);

        $namespaceSuffixByDirectoryClass = ltrim($directoryInNamespacedRoot, '\\');

        $namespaceSuffixByNamespaceBeforeClass = rtrim(
            Strings::substring($namespaceBeforeClass, strlen($namespace)),
            '\\'
        );

        return $namespaceSuffixByDirectoryClass === $namespaceSuffixByNamespaceBeforeClass;
    }

    private function resolveNamespacePartOfClass(string $className, string $shortClassName): string
    {
        return Strings::substring($className, 0, - strlen($shortClassName));
    }

    /**
     * @param string|string[] $directory
     * @return string[]
     */
    private function resolveDirectories($directory): array
    {
        if (! is_array($directory)) {
            return [$directory];
        }

        return $directory;
    }

    private function normalizePath(string $path): string
    {
        return str_replace('/', '\\', $path);
    }
}
