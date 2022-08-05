<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\SymfonyNameToTypeService\NameToTypeServiceReplacer;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\SymfonyNameToTypeService\NameToTypeServiceReplacer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NameToTypeServiceReplacerTest extends TestCase
{
    private NameToTypeServiceReplacer $nameToTypeServiceReplacer;

    protected function setUp(): void
    {
        $this->nameToTypeServiceReplacer = new NameToTypeServiceReplacer();
    }

    /**
     * @param array<string, string> $serviceMap
     *
     * @dataProvider provideData()
     */
    public function test(string $yamlConfigFile, array $serviceMap, string $expectedConfigFile): void
    {
        $fileInfo = new SmartFileInfo($yamlConfigFile);
        $changedFileContent = $this->nameToTypeServiceReplacer->replaceInFileInfo($fileInfo, $serviceMap);

        $this->assertStringEqualsFile($expectedConfigFile, $changedFileContent);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/some_file.yml',
            [
                'some.name' => 'App\SomeType',
            ],
            __DIR__ . '/Fixture/Expected/expected_changed_some_file.yml',
        ];

        yield [
            __DIR__ . '/Fixture/skip_command.yml',
            [
                'some.command' => 'App\Command\SomeCommand',
            ],
            __DIR__ . '/Fixture/skip_command.yml',
        ];

        yield [
            __DIR__ . '/Fixture/security_nested_service_links.yml',
            [
                'security.token_authenticator' => 'App\Specific\SecurityAuthenticator',
            ],
            __DIR__ . '/Fixture/Expected/expected_security_nested_service_links.yml',
        ];
    }
}
