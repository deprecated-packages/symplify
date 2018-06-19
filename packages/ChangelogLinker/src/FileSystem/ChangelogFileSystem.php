<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;

final class ChangelogFileSystem
{
    public function readChangelog(): string
    {
        $changelogFilePath = $this->getChangelogFilePath();
        $this->ensureFileExists($changelogFilePath);

        return FileSystem::read($changelogFilePath);
    }

    // @todo resolve append links
    public function storeChangelog(string $content): void
    {
        FileSystem::write($this->getChangelogFilePath(), $content);
    }

    private function ensureFileExists(string $changelogFilePath): void
    {
        if (file_exists($changelogFilePath)) {
            return;
        }

        throw new FileNotFoundException(sprintf('Changelog file "%s" was not found' . PHP_EOL, $changelogFilePath));
    }

    private function getChangelogFilePath(): string
    {
        return getcwd() . '/CHANGELOG.md';
    }
}
