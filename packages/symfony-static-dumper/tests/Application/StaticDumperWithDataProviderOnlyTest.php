<?php

declare(strict_types=1);

use Symplify\SymfonyStaticDumper\Tests\TestProject\Kernel\TestSymfonyStaticDumperKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;

class StaticDumperWithDataProviderOnlyTest extends AbstractKernelTestCase
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

    public function setUp(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->symfonyStaticDumperApplication = $this->getService(SymfonyStaticDumperApplication::class);

        $this->smartFileSystem = new SmartFileSystem();
    }

    public function testWithProviderDataOnly()
    {
        $this->symfonyStaticDumperApplication->run(__DIR__ . '/../test_project/public', self::OUTPUT_DIRECTORY, true);

        // Controller with Data Providers
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

        // Controllers without Data Providers
        $this->assertFileDoesNotExist(self::OUTPUT_DIRECTORY . '/static/index.html');
        $this->assertFileDoesNotExist(self::OUTPUT_DIRECTORY . '/static.html');
    }
}