<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Application\Command;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $source;

    /**
     * @var bool
     */
    private $isFixer;

    /**
     * @var array
     */
    private $jsonConfiguration = [];

    public function __construct(
        array $source,
        bool $isFixer,
        array $jsonConfiguration
    ) {
        $this->source = $source;
        $this->isFixer = $isFixer;
        $this->jsonConfiguration = $jsonConfiguration;
    }

    public function getSource() : array
    {
        return $this->source;
    }

    public function getJsonConfiguration() : array
    {
        return $this->jsonConfiguration;
    }

    public function isFixer() : bool
    {
        return $this->isFixer;
    }
}
