<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class SniffFactory
{
    /**
     * @var SniffPropertyValueDataCollector
     */
    private $sniffPropertyValueDataCollector;

    public function __construct(
        SniffPropertyValueDataCollector $customSniffPropertyDataCollector
    ) {
        $this->sniffPropertyValueDataCollector = $customSniffPropertyDataCollector;
    }

    /**
     * @return Sniff|null
     */
    public function create(string $sniffClassName)
    {
        $sniff = new $sniffClassName;
        return $this->setCustomSniffPropertyValues($sniff);
    }

    private function setCustomSniffPropertyValues(Sniff $sniff) : Sniff
    {
        $sniffPropertyValues = $this->sniffPropertyValueDataCollector->getForSniff($sniff);
        foreach ($sniffPropertyValues as $property => $value) {
            $sniff->$property = $value;
        }

        return $sniff;
    }
}
