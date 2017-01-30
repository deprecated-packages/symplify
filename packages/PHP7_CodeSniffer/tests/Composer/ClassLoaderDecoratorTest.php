<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Composer;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Composer\ClassLoaderDecorator;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class ClassLoaderDecoratorTest extends TestCase
{
    public function test()
    {
        $classLoaderDecorator = new ClassLoaderDecorator(new StandardFinder());

        $classLoader = new ClassLoader();

        $this->assertCount(0, $classLoader->getPrefixesPsr4());

        $classLoaderDecorator->decorate($classLoader);

        $psr4Prefixes = $classLoader->getPrefixesPsr4();
        $this->assertCount(3, $psr4Prefixes);

        $this->assertArrayHasKey('PHP_CodeSniffer\\', $psr4Prefixes);
        $this->assertStringEndsWith(
            'vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/PHPCS/PHP_CodeSniffer',
            $psr4Prefixes['PHP_CodeSniffer\\'][0]
        );
    }
}
