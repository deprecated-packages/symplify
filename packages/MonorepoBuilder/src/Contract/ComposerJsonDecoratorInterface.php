<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Contract;

interface ComposerJsonDecoratorInterface
{
    public function decorate(array $composerJson): array;
}
