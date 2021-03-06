<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\StaticCall;
use Symplify\Astral\Naming\SimpleNameResolver;

final class RegexStaticCallAnalyzer
{
    /**
     * @var string[]
     */
    private const NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX = ['match', 'matchAll', 'replace', 'split'];

    /**
     * @var string
     */
    private const NETTE_UTILS_STRINGS_CLASS = 'Nette\Utils\Strings';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isRegexStaticCall(StaticCall $staticCall): bool
    {
        if (! $this->simpleNameResolver->isName($staticCall->class, self::NETTE_UTILS_STRINGS_CLASS)) {
            return false;
        }

        return $this->simpleNameResolver->isNames(
            $staticCall->name,
            self::NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX
        );
    }
}
