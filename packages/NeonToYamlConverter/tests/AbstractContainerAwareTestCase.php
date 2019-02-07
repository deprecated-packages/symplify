<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlConverterKernel;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed[] $data
     * @param string|int $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $neonToYamlConverterKernel = new NeonToYamlConverterKernel();
        $neonToYamlConverterKernel->boot();
        $this->container = $neonToYamlConverterKernel->getContainer();

        parent::__construct($name, $data, $dataName);
    }
}
