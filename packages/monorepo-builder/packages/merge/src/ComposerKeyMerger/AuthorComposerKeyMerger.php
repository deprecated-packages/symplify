<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class AuthorComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getAuthors() === []) {
            return;
        }

        $mainAuthors = array_column($mainComposerJson->getAuthors(), null, 'name');
        $newAuthors = array_column($newComposerJson->getAuthors(), null, 'name');

        $authors = array_merge($mainAuthors, $newAuthors);
        $authors = array_values($authors);

        $mainComposerJson->setAuthors($authors);
    }
}
