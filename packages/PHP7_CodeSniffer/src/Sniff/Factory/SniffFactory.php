<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;

final class SniffFactory
{
    /**
     * @var array[][] { sniffClass => { property => value }}
     */
    private $sniffPropertyValues;

    public function setSniffPropertyValues(array $sniffPropertyValues)
    {
        $this->sniffPropertyValues = $sniffPropertyValues;
    }

    public function create(string $sniffClass) : Sniff
    {
        $sniff = new $sniffClass;
        $this->decorateSniffWithValues($sniff, $sniffClass);
        return $sniff;
    }

    private function decorateSniffWithValues(Sniff $sniff, string $sniffClass) : void
    {
        if (!isset($sniffPropertyValues[$sniffClass])) {
            return;
        }

        foreach ($sniffPropertyValues[$sniffClass] as $property => $value) {
            $sniff->$property = $value;
        }
    }
}
