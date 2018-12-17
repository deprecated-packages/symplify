<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @experimental
 */
trait ContainerAwareTestCaseTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Container|null
     */
    private static $cachedContainer;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if (self::$cachedContainer === null) {
            self::$cachedContainer = $this->createContainer();
        }
        $this->container = self::$cachedContainer;

        parent::__construct($name, $data, $dataName);
    }

    abstract protected function getKernelClass(): string;

    private function createContainer(): Container
    {
        $kernelClass = $this->getKernelClass();
        /** @var Kernel $kernel */
        $kernel = new $kernelClass();
        $kernel->boot();

        return $kernel->getContainer();
    }
}
