<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;
use Symplify\ChangelogLinker\LinkAppender;

final class ChangelogFileSystem
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(LinkAppender $linkAppender)
    {
        $this->linkAppender = $linkAppender;
    }

    public function readChangelog(): string
    {
        $changelogFilePath = $this->getChangelogFilePath();
        $this->ensureFileExists($changelogFilePath);

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

    private function ensureFileExists(string $changelogFilePath): void
    {
        if (file_exists($changelogFilePath)) {
            return;
        }

        throw new FileNotFoundException(sprintf('Changelog file "%s" was not found', $changelogFilePath));
    }
}
