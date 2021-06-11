<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\LatteFilter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\StaticCallWithFilterReplacer;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteFilterManager
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private StaticCallWithFilterReplacer $staticCallWithFilterReplacer,
        private SmartFileSystem $smartFileSystem
    ) {
    }
}
