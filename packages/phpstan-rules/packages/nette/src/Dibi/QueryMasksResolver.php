<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Dibi;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;

/**
 * E.g. from "INSERT %s %v" Will extact: s, v
 */
final class QueryMasksResolver
{
    /**
     * @see https://regex101.com/r/tXL1UV/1
     * @var string
     */
    private const MASK_REGEX = '#(?<mask>%\w+)#';

    public function __construct(
        private NodeValueResolver $nodeValueResolver,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveQueryMasks(MethodCall $methodCall, Scope $scope): array
    {
        $firstArg = $methodCall->args[0];

        $queryString = $this->nodeValueResolver->resolve($firstArg->value, $scope->getFile());
        if (! is_string($queryString)) {
            return [];
        }

        // compare only if string contains masks
        if (! str_contains($queryString, '%')) {
            return [];
        }

        $maskMatches = Strings::matchAll($queryString, self::MASK_REGEX);

        $masks = [];
        foreach ($maskMatches as $maskMatch) {
            $masks[] = $maskMatch['mask'];
        }

        return $masks;
    }
}
