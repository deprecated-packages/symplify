<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Composer;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Composer\ClassLoaderDecorator;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class ClassLoaderDecoratorTest extends TestCase
{
    /**
     * @var ClassLoaderDecorator
     */
    private $classLoaderDecorator;

    protected function setUp()
    {
        $this->classLoaderDecorator = new ClassLoaderDecorator(new StandardFinder());
    }

    public function testDecorate()
    {
        $classLoader = new ClassLoader();
        $this->assertCount(0, $classLoader->getPrefixesPsr4());

        $this->classLoaderDecorator->decorate($classLoader);

        $psr4Prefixes = $classLoader->getPrefixesPsr4();
        $this->assertCount(1, $psr4Prefixes);
        $this->assertArrayHasKey('SlevomatCodingStandard\\', $psr4Prefixes);
        $this->assertContains(
            'vendor/slevomat/coding-standard/SlevomatCodingStandard',
            $psr4Prefixes['SlevomatCodingStandard\\'][0]
        );
    }
}
