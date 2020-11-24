<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\ValueObject;

use Symplify\PHPMDDecomposer\ValueObject\Config\PHPStanConfig;

final class DecomposedFileConfigs
{
    /**
     * @var PHPStanConfig
     */
    private $phpStanConfig;

    /**
     * @var string
     */
    private $ecsFileContent;

    /**
     * @var string
     */
    private $rectorFileContent;

    public function __construct(PHPStanConfig $phpStanConfig, string $ecsFileContent, string $rectorFileContent)
    {
        $this->phpStanConfig = $phpStanConfig;
        $this->ecsFileContent = $ecsFileContent;
        $this->rectorFileContent = $rectorFileContent;
    }

    public function getPHPStanConfig(): PHPStanConfig
    {
        return $this->phpStanConfig;
    }

    public function getEcsFileContent(): string
    {
        return $this->ecsFileContent;
    }

    public function getRectorFileContent(): string
    {
        return $this->rectorFileContent;
    }
}
