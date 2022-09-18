<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\ReplaceRepositoriesRelativePathComposerJsonDecorator\ReplaceRepositoriesRelativePathComposerJsonDecoratorTest
 */
final class NormalizeRepositoriesPathComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    private const UP_DIRECTORY = '../';

    public function __construct() { }

    public function decorate(ComposerJson $composerJson): void
    {
        $this->processReplaceRepositoriesRelativePath($composerJson);
    }

    private function processReplaceRepositoriesRelativePath(ComposerJson $composerJson): void
    {
        $repositories = $composerJson->getRepositories();

        foreach ($repositories as $index => $repository) {
            if ($repository['type'] !== 'path') {
                continue;
            }

            $repositories[$index]['url'] = str_replace(self::UP_DIRECTORY, '', $repository['url']);
        }

        $composerJson->setRepositories($repositories);
    }
}
