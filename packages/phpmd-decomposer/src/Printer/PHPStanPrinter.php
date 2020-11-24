<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Printer;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symplify\PHPMDDecomposer\ValueObject\Config\PHPStanConfig;

final class PHPStanPrinter
{
    public function printPHPStanConfig(PHPStanConfig $phpStanConfig): string
    {
        $config = [];
        if ($phpStanConfig->getIncludes() !== []) {
            $config['includes'] = $phpStanConfig->getIncludes();
        }

        // @see https://phpstan.org/user-guide/ignoring-errors#excluding-whole-files
        if ($phpStanConfig->getParameters() !== []) {
            $config['parameters'] = $phpStanConfig->getParameters();
        }

        if ($phpStanConfig->getRules() !== []) {
            $config['rules'] = $phpStanConfig->getRules();
        }

        $content = Neon::encode($config, Encoder::BLOCK);

        // spaces over tabs
        $content = Strings::replace($content, "#\t#", '    ');
        return rtrim($content) . PHP_EOL;
    }
}
