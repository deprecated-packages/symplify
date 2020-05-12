<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;

final class ChangelogFileSystem
{
    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogPlaceholderGuard
     */
    private $changelogPlaceholderGuard;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(
        ChangelogLinker $changelogLinker,
        ChangelogPlaceholderGuard $changelogPlaceholderGuard,
        FileSystemGuard $fileSystemGuard,
        ParameterProvider $parameterProvider
    ) {
        $this->changelogLinker = $changelogLinker;
        $this->changelogPlaceholderGuard = $changelogPlaceholderGuard;
        $this->parameterProvider = $parameterProvider;
        $this->fileSystemGuard = $fileSystemGuard;
    }

    public function readChangelog(): string
    {
        $changelogFilePath = $this->getChangelogFilePath();
        $this->fileSystemGuard->ensureFileExists($changelogFilePath, __METHOD__);

        return FileSystem::read($changelogFilePath);
    }

    public function storeChangelog(string $content): void
    {
        FileSystem::write($this->getChangelogFilePath(), $content);
    }

    public function addToChangelogOnPlaceholder(string $newContent, string $placeholder): void
    {
        $changelogContent = $this->readChangelog();

        $this->changelogPlaceholderGuard->ensurePlaceholderIsPresent($changelogContent, $placeholder);

        $contentToWrite = sprintf(
            '%s%s%s<!-- dumped content start -->%s%s<!-- dumped content end -->',
            $placeholder,
            PHP_EOL,
            PHP_EOL,
            PHP_EOL,
            $newContent
        );

        $updatedChangelogContent = str_replace($placeholder, $contentToWrite, $changelogContent);

        $updatedChangelogContent = $this->changelogLinker->processContentWithLinkAppends($updatedChangelogContent);

        $this->storeChangelog($updatedChangelogContent);
    }

    private function getChangelogFilePath(): string
    {
        $fileParameter = $this->parameterProvider->provideParameter(Option::FILE);
        if (is_string($fileParameter) && file_exists($fileParameter)) {
            return $fileParameter;
        }

        return getcwd() . '/CHANGELOG.md';
    }
}
