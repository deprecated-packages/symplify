<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\ValueObject\Config;

use DOMElement;
use Nette\Utils\Strings;

final class MatchToPHPStanConfig
{
    /**
     * @var string
     */
    private $match;

    /**
     * @var PHPStanConfig
     */
    private $phpStanConfig;

    public function __construct(string $match, PHPStanConfig $phpStanConfig)
    {
        $this->match = $match;
        $this->phpStanConfig = $phpStanConfig;
    }

    public function isMatch(DOMElement $domElement): bool
    {
        if ($domElement->hasAttribute('ref')) {
            return $domElement->getAttribute('ref') === $this->match;
        }

        if ($domElement->hasAttribute('name')) {
            return Strings::endsWith($this->match, $domElement->getAttribute('name'));
        }

        return false;
    }

    public function getConfig(): PHPStanConfig
    {
        return $this->phpStanConfig;
    }
}
