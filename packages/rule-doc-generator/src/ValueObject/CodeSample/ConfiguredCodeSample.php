<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Rector\Core\Exception\Configuration\InvalidConfigurationException;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ConfiguredCodeSample extends AbstractCodeSample implements CodeSampleInterface
{
    /**
     * @var array<string, mixed>
     */
    private $configuration = [];

    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(string $badCode, string $goodCode, array $configuration)
    {
        if ($configuration === []) {
            throw new InvalidConfigurationException('Configuration cannot be empty');
        }

        $this->configuration = $configuration;

        parent::__construct($badCode, $goodCode);
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
