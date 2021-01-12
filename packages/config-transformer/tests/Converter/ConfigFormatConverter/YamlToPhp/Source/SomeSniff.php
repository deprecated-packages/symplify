<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\YamlToPhp\Source;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class SomeSniff implements Sniff
{
    public function register()
    {
    }

    public function process(File $phpcsFile, $stackPtr)
    {
    }
}
