<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Contract;

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJson;

interface ComposerJsonDecoratorInterface
{
    public function decorate(ComposerJson $composerJson): void;
}
