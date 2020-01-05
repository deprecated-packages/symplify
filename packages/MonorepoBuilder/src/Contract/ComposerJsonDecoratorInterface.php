<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Contract;

interface ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array;
}
