<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Style;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyStyleFactory
{
    public static function create(): SymfonyStyle
    {
        return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput);
    }
}
