<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\Application;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;
use Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel\TestSymfonyStaticDumperKernel;

final class SymfonyStaticDumperApplicationTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const EXPECTED_DIRECTORY = __DIR__ . '/../Fixture/expected';

    /**
     * @var string
     */
    private const OUTPUT_DIRECTORY = __DIR__ . '/../temp/output';

    private SymfonyStaticDumperApplication $symfonyStaticDumperApplication;

    private SmartFileSystem $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->symfonyStaticDumperApplication = $this->getService(SymfonyStaticDumperApplication::class);

        $this->smartFileSystem = new SmartFileSystem();

        $this->symfonyStaticDumperApplication->run(__DIR__ . '/../test_project/public', self::OUTPUT_DIRECTORY);
    }

    protected function tearDown(): void
    {
        $this->smartFileSystem->remove(self::OUTPUT_DIRECTORY);
    }

    public function testCssIsDumped(): void
    {
        // css
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/some.css');
        $this->assertFileEquals(self::EXPECTED_DIRECTORY . '/some.css', self::OUTPUT_DIRECTORY . '/some.css');
    }

    public function testRenderHtml(): void
    {
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/kedlubna/index.html');
        $this->assertFileEquals(
            self::EXPECTED_DIRECTORY . '/kedlubna/index.html',
            self::OUTPUT_DIRECTORY . '/kedlubna/index.html'
        );
    }

    public function testRenderJson(): void
    {
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/api.json');

        $expectedFileContent = $this->smartFileSystem->readFile(self::EXPECTED_DIRECTORY . '/api.json');
        $expectedFileContent = trim($expectedFileContent);

        $outputFileContent = $this->smartFileSystem->readFile(self::OUTPUT_DIRECTORY . '/api.json');
        $outputFileContent = trim($outputFileContent);

        $this->assertSame($expectedFileContent, $outputFileContent);
    }

    public function testRenderPageWithTemplateController(): void
    {
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
        // Controller with 2 args
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/test/1/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/test/2/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/foo/1/index.html');
        $this->assertFileExists(self::OUTPUT_DIRECTORY . '/foo/2/index.html');

        foreach (['test', 'foo'] as $type) {
            foreach ([1, 2] as $param) {
                $this->assertFileEquals(
                    self::EXPECTED_DIRECTORY . '/two-params/' . $type . $param . '.html',
                    self::OUTPUT_DIRECTORY . sprintf('/%s/%d/index.html', $type, $param)
                );
            }
        }
    }
}
