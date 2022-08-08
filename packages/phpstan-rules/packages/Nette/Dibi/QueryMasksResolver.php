<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Dibi;

use Nette\Utils\Strings;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * E.g. from "INSERT %s %v" Will extact: s, v
 */
final class QueryMasksResolver
{
    /**
     * @see https://regex101.com/r/tXL1UV/1
     * @var string
     */
    private const MASK_REGEX = '#(?<mask>%\w+)\b#';

    /**
     * @return string[]
     */
    public function resolveQueryMasks(MethodCall $methodCall, Scope $scope): array
    {
        $firstArg = $methodCall->args[0];
        if (! $firstArg instanceof Arg) {
            return [];
        }

        $queryStringType = $scope->getType($firstArg->value);
        if (! $queryStringType instanceof ConstantStringType) {
            return [];
        }

        $queryString = $queryStringType->getValue();
        return $this->resolveMasksFromString($queryString);
    }

    public function resolveSingleQueryMask(Expr|null $expr, Scope $scope): ?string
    {
        if ($expr === null) {
            return null;
        }

        $exprType = $scope->getType($expr);
        if (! $exprType instanceof ConstantStringType) {
            return null;
        }

        $dimValue = $exprType->getValue();
        if (! is_string($dimValue)) {
            return null;
        }

        $masks = $this->resolveMasksFromString($dimValue);
        if (count($masks) !== 1) {
            return null;
        }

        return $masks[0];
    }

    /**
     * @return string[]
     */
    private function resolveMasksFromString(string $queryString): array
    {
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
