<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule\CheckNotTestsNamespaceOutsideTestsDirectoryRuleTest
 */
final class CheckNotTestsNamespaceOutsideTestsDirectoryRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    private const ERROR_NAMESPACE_OUTSIDE_TEST_DIR = '"Tests" namespace (%s) used outside of "tests" directory (%s)';

    /**
     * @var string
     */
    private const ERROR_TEST_FILE_OUTSIDE_NAMESPACE = 'Test file (%s) is outside of "Tests" namespace (%s)';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Namespace_::class];
    }

    /**
     * @param Namespace_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        $fileInfo = new SmartFileInfo($scope->getFile());

        if (! $this->hasTestsNamespace($node->name)) {
            if ($this->hasTestSuffix($scope)) {
                $errorMessage = sprintf(
                    self::ERROR_TEST_FILE_OUTSIDE_NAMESPACE,
                    $fileInfo->getRelativeFilePathFromCwd(),
                    $node->name->toString()
                );
                return [$errorMessage];
            }

            return [];
        }

        if ($this->isInTestsDirectory($scope)) {
            return [];
        }

        $errorMessage = sprintf(
            self::ERROR_NAMESPACE_OUTSIDE_TEST_DIR,
            $node->name->toString(),
            $fileInfo->getRelativeFilePathFromCwd()
        );

        return [$errorMessage];
    }

    private function hasTestsNamespace(Name $name): bool
    {
        return in_array('Tests', $name->parts, true);
    }

    private function hasTestSuffix(Scope $scope): bool
    {
        return Strings::endsWith($scope->getFile(), 'Test.php');
    }

    private function isInTestsDirectory(Scope $scope): bool
    {
        return Strings::contains($scope->getFile(), '/tests/');
    }
}
