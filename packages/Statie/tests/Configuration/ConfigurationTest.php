<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class ConfigurationTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
    }

    public function testAddGlobalVariable(): void
    {
        $this->configuration->addGlobalVarialbe('key', 'value');

        $this->assertSame([
            'key' => 'value',
        ], $this->configuration->getOptions());
    }
}
