<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\SymfonyNameToTypeService\XmlServiceMapFactory;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\SymfonyNameToTypeService\XmlServiceMapFactory;

final class XmlServiceMapFactoryTest extends TestCase
{
    private XmlServiceMapFactory $xmlServiceMapFactory;

    protected function setUp(): void
    {
        $this->xmlServiceMapFactory = new XmlServiceMapFactory();
    }

    public function test(): void
    {
        $serviceMap = $this->xmlServiceMapFactory->create(__DIR__ . '/Fixture/dumped_container.xml');

        $this->assertSame([
            'some_name' => 'AppBundle\SomeType',
            'another_name' => 'AppBundle\AnotherType',
        ], $serviceMap);
    }
}
