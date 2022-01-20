<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Testing\Printer;

use Nette\Utils\Strings;

final class PHPUnitXmlPrinter
{
    /**
     * Prints lists of <file> elements in https://phpunit.readthedocs.io/en/9.5/configuration.html#the-testsuite-element
     *
     * @param string[] $filePaths
     */
    public function printFiles(array $filePaths, string $rootDirectory): string
    {
        $rootDirectory = realpath($rootDirectory);

        $fileContents = '';
        foreach ($filePaths as $filePath) {
            $relativeFilePath = Strings::after($filePath, $rootDirectory . '/');

            $fileContents .= '<file>' . $relativeFilePath . '</file>' . PHP_EOL;
        }

        return $fileContents;
    }
}
