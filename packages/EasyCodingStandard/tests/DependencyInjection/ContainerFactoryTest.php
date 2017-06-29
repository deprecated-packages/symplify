<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\TestCase;

final class ContainerFactoryTest extends TestCase
{
    /**
     * @var ContainerFactory
     */
    private $containerFactory;

    protected function setUp(): void
    {
        $this->containerFactory = new ContainerFactory;
    }

    public function test(): void
    {
        $this->containerFactory->createWithConfig(__DIR__ . '/fixtures/including.neon');
    }
}
