<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\Tests\DI\MigrationsExtension;

use PHPUnit\Framework\TestCase;
use Symplify\DoctrineMigrations\Tests\ContainerFactory;

final class EnsureRequiredExtensionsTest extends TestCase
{
    /**
     * @expectedException \Symplify\DoctrineMigrations\Exception\DI\MissingExtensionException
     */
    public function testEnsureEventDispatcher(): void
    {
        (new ContainerFactory)->createWithConfig(__DIR__ . '/../../config/extensionOnly.neon');
    }
}
