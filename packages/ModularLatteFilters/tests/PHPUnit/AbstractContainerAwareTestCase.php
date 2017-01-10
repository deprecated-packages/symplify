<?php

declare(strict_types = 1);

namespace Zenify\ModularLatteFilters\Tests\PHPUnit;

use Nette\Database\Context;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Zenify\ModularLatteFilters\Tests\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{

    /**
     * @var Container[]
     */
    private static $containers = [];


    /**
     * @return object
     */
    protected function getServiceByType(string $class)
    {
        return $this->getContainer()
            ->getByType($class);
    }


    private function getContainer(): Container
    {
        if (isset(self::$containers[$this->getTestDirectory()])) {
            return self::$containers[$this->getTestDirectory()];
        }

        $container = (new ContainerFactory)->createWithConfig($this->getTestDirectory() . '/config/config.neon');

        self::$containers[$this->getTestDirectory()] = $container;

        return $container;
    }


    private function getTestDirectory(): string
    {
        $testFilename = (new ReflectionClass($this))->getFileName();
        return dirname($testFilename);
    }
}
