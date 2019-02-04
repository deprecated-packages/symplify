<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;

abstract class AbstractConfigAwareContainerTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed[]    $data
     * @param int|string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        parent::__construct($name, $data, $dataName);
    }

    abstract protected function provideConfig(): string;
}
