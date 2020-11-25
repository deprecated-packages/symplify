<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

final class MarkdownCodeWrapper
{
    /**
     * @var string
     */
    private const SYNTAX_JSON = 'json';

    /**
     * @var string
     */
    private const SYNTAX_PHP = 'php';

    /**
     * @var string
     */
    private const SYNTAX_YAML = 'yaml';

    public function printJsonCode(string $content): string
    {
        return $this->printCodeWrapped($content, self::SYNTAX_JSON);
    }

    public function printPhpCode(string $content): string
    {
        return $this->printCodeWrapped($content, self::SYNTAX_PHP);
    }

    public function printYamlCode(string $content): string
    {
        return $this->printCodeWrapped($content, self::SYNTAX_YAML);
    }

    private function printCodeWrapped(string $content, string $format): string
    {
        return sprintf('```%s%s%s%s```', $format, PHP_EOL, rtrim($content), PHP_EOL) . PHP_EOL;
    }
}
