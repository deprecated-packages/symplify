<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem;

use Nette\Utils\Html;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class SmartFileSystem extends Filesystem
{
    /**
     * @see https://github.com/symfony/filesystem/pull/4/files
     */
    public function readFile(string $filename): string
    {
        $source = @file_get_contents($filename);
        if ($source === false) {
            $message = sprintf('Failed to read "%s" file: "%s"', $filename, $this->getLastError());

            throw new IOException($message, 0, null, $filename);
        }

        return $source;
    }

    /**
     * Returns the last PHP error as plain string.
     * @source https://github.com/nette/utils/blob/ab8eea12b8aacc7ea5bdafa49b711c2988447994/src/Utils/Helpers.php#L31-L40
     */
    private function getLastError(): string
    {
        $message = error_get_last()['message'] ?? '';
        $message = ini_get('html_errors') ? Html::htmlToText($message) : $message;
        return preg_replace('#^\w+\(.*?\): #', '', $message);
    }
}
