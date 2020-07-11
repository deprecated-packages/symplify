<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\Application;

use Nette\Utils\FileSystem;

final class SymfonyStaticDumperApplicationTest extends AbstractSymfonyStaticDumperTestCase
{
    public function testCssIsDumped(): void
    {
        $this->application()->copyAssets(__DIR__ . '/../test_project/public', self::OUTPUT_DIRECTORY);

        // css
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/some.css');
        $this->assertFileEquals(self::EXPECTED_DIRECTORY . '/some.css', self::OUTPUT_DIRECTORY . '/some.css');
    }

    public function testRenderHtml(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY);

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/kedlubna/index.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/kedlubna/index.html',
            self::OUTPUT_DIRECTORY . '/kedlubna/index.html'
        );
    }

    public function testRenderOnlySpecificRoute(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY, ['kedlubna']);

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/kedlubna/index.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/kedlubna/index.html',
            self::OUTPUT_DIRECTORY . '/kedlubna/index.html'
        );
        $this->assertFileDoesNotExist(self::OUTPUT_DIRECTORY . '/api.json');
        $this->assertFileDoesNotExist(self::OUTPUT_DIRECTORY . '/static/index.html');
        $this->assertFileDoesNotExist(self::OUTPUT_DIRECTORY . '/index.html');
    }

    public function testRenderJson(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY);

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/api.json');

        $expectedFileContent = FileSystem::read(self::EXPECTED_DIRECTORY . '/api.json');
        $expectedFileContent = trim($expectedFileContent);

        $outputFileContent = FileSystem::read(self::OUTPUT_DIRECTORY . '/api.json');
        $outputFileContent = trim($outputFileContent);

        $this->assertSame($expectedFileContent, $outputFileContent);
    }

    public function testRenderPageWithTemplateController(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY);

        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/static/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/static.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/static.html',
            self::OUTPUT_DIRECTORY . '/static/index.html'
        );
        $this->assertFileEquals(
            self::OUTPUT_DIRECTORY . '/static.html',
            self::OUTPUT_DIRECTORY . '/static/index.html'
        );
    }

    public function testRenderControllerWithOneArgument(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY);

        // controller with 1 arg
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/one-param/1/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/one-param/2/index.html');

        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/one-param/1.html',
            self::OUTPUT_DIRECTORY . '/one-param/1/index.html'
        );
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/one-param/2.html',
            self::OUTPUT_DIRECTORY . '/one-param/2/index.html'
        );
    }

    public function testRenderControllerWithMultipleArguments(): void
    {
        $this->application()->dumpControllers(self::OUTPUT_DIRECTORY);

        // Controller with 2 args
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/test/1/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/test/2/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/foo/1/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/foo/2/index.html');

        foreach (['test', 'foo'] as $type) {
            foreach ([1, 2] as $param) {
                $this->assertFileEquals(
                    self::EXPECTED_DIRECTORY . '/two-params/' . $type . $param . '.html',
                    self::OUTPUT_DIRECTORY . sprintf('/%s/%s/index.html', $type, $param)
                );
            }
        }
    }
}
