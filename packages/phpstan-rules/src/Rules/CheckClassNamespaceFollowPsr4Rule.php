<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Composer\ComposerAutoloadResolver;
use Symplify\PHPStanRules\Composer\Psr4PathValidator;
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
    public const ERROR_MESSAGE = 'Class like namespace "%s" does not follow PSR-4 configuration in composer.json';

    /**
     * @var array<string, string|string[]>
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

    /**
     * @var Psr4PathValidator
     */
    private $psr4PathValidator;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ComposerAutoloadResolver $composerAutoloadResolver,
        DirectoryChecker $directoryChecker,
        Psr4PathValidator $psr4PathValidator
    ) {
        $this->autoloadPsr4Paths = $composerAutoloadResolver->getPsr4Autoload();
        $this->simpleNameResolver = $simpleNameResolver;
        $this->directoryChecker = $directoryChecker;
        $this->psr4PathValidator = $psr4PathValidator;
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

        $shortClassName = $this->simpleNameResolver->getShortClassNameFromNode($node);
        if ($shortClassName === null) {
            return [];
        }

        $file = (string) str_replace('\\', '/', $scope->getFile());
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

                if ($this->psr4PathValidator->isClassNamespaceCorrect(
                    $namespace,
                    $singleDirectory,
                    $namespaceBeforeClass,
                    $file
                )) {
                    return [];
                }
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
}
