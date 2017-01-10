<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\DI\MigrationsExtension;

use PHPUnit\Framework\TestCase;
use Zenify\DoctrineMigrations\Tests\ContainerFactory;

final class EnsureRequiredExtensionsTest extends TestCase
{

    /**
     * @expectedException \Zenify\DoctrineMigrations\Exception\DI\MissingExtensionException
     */
    public function testEnsureEventDispatcher()
    {
        (new ContainerFactory)->createWithConfig(__DIR__ . '/../../config/extensionOnly.neon');
    }
}
