<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;

final class BoolishNameAnalyser
{
    /**
     * @var string[]
     */
    private const BOOL_PREFIXES = [
        'is',
        'are',
        'was',
        'will',
        'must',
        'has',
        'have',
        'had',
        'do',
        'does',
        'di',
        'can',
        'could',
        'should',
        'starts',
        'contains',
        'ends',
        'exists',
        'supports',
        'provide',
        'detect',
        # array access
        'offsetExists',
        'equals',
        'match',
        'matches',
    ];

    public function isBoolish(string $methodName): bool
    {
        $prefixesPattern = '#^(' . implode('|', self::BOOL_PREFIXES) . ')#';

        return (bool) Strings::match($methodName, $prefixesPattern);
    }
}
