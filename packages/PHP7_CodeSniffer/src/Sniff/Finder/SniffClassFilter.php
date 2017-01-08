<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Finder;

use ReflectionClass;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;

final class SniffClassFilter
{
    public function filterOutAbstractAndNonPhpSniffClasses(array $originSniffClasses) : array
    {
        $finalSniffClasses = [];
        foreach ($originSniffClasses as $sniffClass) {
            if ($this->isAbstractClass($sniffClass)) {
                continue;
            }

            if (!$this->doesSniffSupportsPhp($sniffClass)) {
                continue;
            }

            $sniffCode = SniffNaming::guessCodeByClass($sniffClass);
            $finalSniffClasses[$sniffCode] = $sniffClass;
        }

        return $finalSniffClasses;
    }

    private function isAbstractClass(string $className) : bool
    {
        return (new ReflectionClass($className))->isAbstract();
    }

    private function doesSniffSupportsPhp(string $className) : bool
    {
        $vars = get_class_vars($className);
        if (!isset($vars['supportedTokenizers'])) {
            return true;
        }

        if (in_array('PHP', $vars['supportedTokenizers'])) {
            return true;
        }

        return false;
    }
}
