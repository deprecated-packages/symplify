<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;

final class ConfiguredCodeSample extends AbstractCodeSample implements CodeSampleInterface
{
    /**
     * @var mixed[]
     */
    private $configuration = [];

    /**
     * @param mixed[] $configuration
     */
    public function __construct(string $goodCode, string $badCode, array $configuration)
    {
        $this->configuration = $configuration;

        parent::__construct($goodCode, $badCode);
    }

    /**
     * @return mixed[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
