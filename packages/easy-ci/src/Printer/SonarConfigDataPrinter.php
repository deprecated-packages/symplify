<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Printer;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;

final class SonarConfigDataPrinter
{
    public function print(array $sonarFileData): string
    {
        $fileContent = '';

        foreach ($sonarFileData as $key => $value) {
            $stringValue = $this->createStringValue($value, $key);
            if ($stringValue === '') {
                continue;
            }

            $fileContent = $this->appendKeyLine($fileContent, $key, $stringValue);
        }

        return rtrim($fileContent) . PHP_EOL;
    }

    private function appendKeyLine(string $fileContent, string $key, string $line): string
    {
        $fileContent .= sprintf('%s=%s', $key, $line);
        $fileContent .= PHP_EOL . PHP_EOL;

        return $fileContent;
    }

    /**
     * @param mixed $value
     */
    private function createStringValue($value, string $key): string
    {
        if (is_array($value)) {
            return implode(',', $value);
        }

        if (is_string($value)) {
            return $value;
        }

        $message = sprintf('Sonar config value for "%s" must be string', $key);
        throw new ShouldNotHappenException($message);
    }
}
