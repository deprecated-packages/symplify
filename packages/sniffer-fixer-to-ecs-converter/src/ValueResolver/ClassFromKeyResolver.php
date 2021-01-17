<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\ValueResolver;

final class ClassFromKeyResolver
{
    public function resolveFromStringName(string $ruleId): string
    {
        $ruleIdParts = explode('.', $ruleId);

        $ruleNameParts = [$ruleIdParts[0], 'Sniffs', $ruleIdParts[1], $ruleIdParts[2] . 'Sniff'];

        $sniffClass = implode('\\', $ruleNameParts);
        if (class_exists($sniffClass)) {
            return $sniffClass;
        }

        $coreSniffClass = 'PHP_CodeSniffer\Standards\\' . $sniffClass;
        if (class_exists($coreSniffClass)) {
            return $coreSniffClass;
        }

        return $sniffClass;
    }
}
