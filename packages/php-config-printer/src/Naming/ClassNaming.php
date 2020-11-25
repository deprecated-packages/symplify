<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Naming;

use Nette\Utils\Strings;

final class ClassNaming
{
    public function getShortName(string $class): string
    {
        if (Strings::contains($class, '\\')) {
            return (string) Strings::after($class, '\\', -1);
        }

        return $class;
    }
}
