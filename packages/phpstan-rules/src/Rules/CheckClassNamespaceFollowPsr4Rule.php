<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\SmartFileSystem\SmartFileSystem;
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
    public const ERROR_MESSAGE = 'Class namespace %s does not follow PSR-4 configuration in composer.json, use %s instead';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var string
     */
    private $psr4;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->psr4 = $this->getPsr4Autoload($smartFileSystem);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->psr4 === [] || $node->name === null) {
            return [];
        }

        $namespacedName = (string) $node->namespacedName;
        $className      = (string) $node->name;
        $namespace      = substr($namespacedName, 0, - (strlen($className) + 1));

        return [];
    }

    private function getPsr4Autoload(SmartFileSystem $smartFileSystem): array
    {
        $composerJsonFile = './composer.json';
        if (! file_exists($composerJsonFile)) {
            return [];
        }

        $composerJsonContent = json_decode($smartFileSystem->readFile($composerJsonFile), true);
        $autoloadPsr4 = $composerJsonContent['autoload']['psr-4'] ?? [];
        $autoloadDevPsr4 = $composerJsonContent['autoload-dev']['psr-4'] ?? [];

        return $autoloadPsr4 + $autoloadDevPsr4;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// defined "Foo\\Bar" in composer.json
namespace Foo;

class Baz
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// defined "Foo\\Bar" in composer.json
namespace Foo\Bar;

class Baz
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
