<?php
declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;

final class ComposerFactory
{
    public function create(): Composer
    {
        return Factory::create(new NullIO(), __DIR__ . '/../../composer.json');
    }
}
