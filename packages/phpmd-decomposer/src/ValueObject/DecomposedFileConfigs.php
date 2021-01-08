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

    public function __construct(PHPStanConfig $phpStanConfig)
    {
        $this->phpStanConfig = $phpStanConfig;
    }

    public function getPHPStanConfig(): PHPStanConfig
    {
        return $this->phpStanConfig;
    }
}
