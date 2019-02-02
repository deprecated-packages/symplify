<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;
use Symplify\ChangelogLinker\Exception\MissingPlaceholderInChangelogException;

final class ChangelogFileSystemGuard
{
    public function ensureFileExists(string $changelogFilePath): void
    {
        if (file_exists($changelogFilePath)) {
            return;
        }

        throw new FileNotFoundException(sprintf('Changelog file "%s" was not found', $changelogFilePath));
    }

    public function ensurePlaceholderIsPresent(string $changelogContent, string $placeholder): void
    {
        if (Strings::contains($changelogContent, $placeholder)) {
            return;
        }

        throw new MissingPlaceholderInChangelogException(sprintf(
            'There is missing "%s" placeholder in CHANGELOG.md. Put it where you want to add dumped merges.',
            $placeholder
        ));
    }
}
