<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Application\Command;

use Symplify\SniffRunner\Exception\Configuration\OptionResolver\SourceNotFoundException;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $source;

    /**
     * @var array
     */
    private $standards;

    /**
     * @var array
     */
    private $sniffs;

    /**
     * @var array
     */
    private $excludedSniffs;

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(
        array $source,
        array $standards,
        array $sniffs,
        array $excludedSniffs,
        bool $isFixer
    ) {
        $this->setSource($source);
        $this->standards = $standards;
        $this->sniffs = $sniffs;
        $this->excludedSniffs = $excludedSniffs;
        $this->isFixer = $isFixer;
    }

    public function getSource() : array
    {
        return $this->source;
    }

    public function getStandards() : array
    {
        return $this->standards;
    }

    public function getSniffs() : array
    {
        return $this->sniffs;
    }

    public function getExcludedSniffs() : array
    {
        return $this->excludedSniffs;
    }

    public function isFixer() : bool
    {
        return $this->isFixer;
    }

    private function setSource(array $source) : void
    {
        $this->ensureSourceExists($source);
        $this->source = $source;
    }

    private function ensureSourceExists(array $source) : void
    {
        foreach ($source as $singleSource) {
            if ( ! file_exists($singleSource)) {
                throw new SourceNotFoundException(sprintf(
                    'Source "%s" does not exist.',
                    $singleSource
                ));
            }
        }
    }
}
