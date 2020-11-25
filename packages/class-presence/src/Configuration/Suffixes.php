<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Configuration;

final class Suffixes
{
    /**
     * @var string[]
     */
    private const SUFFIXES = ['yml', 'yaml', 'twig', 'latte', 'neon', 'php'];

    /**
     * @return string[]
     */
    public function provide(): array
    {
        return self::SUFFIXES;
    }

    public function provideRegex(): string
    {
        return '#' . implode('|', self::SUFFIXES) . '#';
    }
}
