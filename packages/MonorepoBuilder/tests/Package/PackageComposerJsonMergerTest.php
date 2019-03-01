<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

final class PackageComposerJsonMergerTest extends AbstractMergeTestCase
{
    public function test(): void
    {
        $expectedJson = [
            'require' => [
                'phpunit/phpunit' => '^2.0',
                'rector/rector' => '^2.0',
                'symplify/symplify' => '^2.0',
            ],
            'autoload' => [
                'psr-4' => [
                    'Symplify\MonorepoBuilder\\' => 'src',
                    'Symplify\Statie\\' => 'src',
                ],
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/Source', $expectedJson);
    }

    public function testUniqueRepositories(): void
    {
        $expectedJson = [
            'repositories' => [[
                'type' => 'composer',
                'url' => 'https://packages.example.org/',
            ]],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceUniqueRepositories', $expectedJson);
    }
}
