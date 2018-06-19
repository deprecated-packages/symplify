<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\ChangelogLinker\LinkAppender;

final class ChangelogFileSystem
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var ChangelogFileSystemGuard
     */
    private $changelogFileSystemGuard;

    public function __construct(LinkAppender $linkAppender, ChangelogFileSystemGuard $changelogFileSystemGuard)
    {
        $this->linkAppender = $linkAppender;
        $this->changelogFileSystemGuard = $changelogFileSystemGuard;
    }

    public function readChangelog(): string
    {
        $changelogFilePath = $this->getChangelogFilePath();
        $this->changelogFileSystemGuard->ensureFileExists($changelogFilePath);

        return FileSystem::read($changelogFilePath);
    }

    public function storeChangelog(string $content): void
    {
        if ($this->linkAppender->getLinksToAppend()) {
            $content .= PHP_EOL . $this->linkAppender->getLinksToAppend();
        }

        FileSystem::write($this->getChangelogFilePath(), $content);
    }

    private function getChangelogFilePath(): string
    {
        return getcwd() . '/CHANGELOG.md';
    }

    public function addToChangelogOnPlaceholder(string $newContent, string $placeholder): void
    {
        $changelogContent = $this->readChangelog();

        $this->changelogFileSystemGuard->ensurePlaceholderIsPresent($changelogContent, $placeholder);

        $contentToWrite = sprintf(
            '%s%s%s<!-- dumped content start -->%s%s<!-- dumped content end -->%s',
            $placeholder,
            PHP_EOL,
            PHP_EOL,
            PHP_EOL,
            $newContent,
            PHP_EOL
        );

        $updatedChangelogContent = str_replace($placeholder, $contentToWrite, $changelogContent);

        $this->storeChangelog($updatedChangelogContent);
    }
}
