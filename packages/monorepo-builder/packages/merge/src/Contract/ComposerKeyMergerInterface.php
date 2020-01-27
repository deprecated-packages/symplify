<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Contract;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;

interface ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newToMerge): void;
}
