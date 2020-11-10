<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

final class MarkdownCodeWrapper
{
    public function printPhpCode(string $content): string
    {
        return $this->printCodeWrapped($content, 'php');
    }

    public function printYamlCode(string $content): string
    {
        return $this->printCodeWrapped($content, 'yaml');
    }

    private function printCodeWrapped(string $content, string $format): string
    {
        return sprintf('```%s%s%s%s```', $format, PHP_EOL, rtrim($content), PHP_EOL) . PHP_EOL;
    }
}
