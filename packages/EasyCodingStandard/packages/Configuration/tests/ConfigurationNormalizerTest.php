<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;

final class ConfigurationNormalizerTest extends TestCase
{
    /**
     * @var CheckerConfigurationNormalizer
     */
    private $configurationNormalizer;

    protected function setUp(): void
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
    }

    public function test(): void
    {
        $normalizedConfiguration = $this->configurationNormalizer->normalize([
            0 => 'sniff',
            'someSniffWithCommentedConfig' => null,
            'sniffAndItsConfig' => ['key' => 'value'],
        ]);

        $this->assertSame([
            'sniff' => [],
            'someSniffWithCommentedConfig' => [],
            'sniffAndItsConfig' => [
                'key' => 'value',
            ],
        ], $normalizedConfiguration);
    }
}
