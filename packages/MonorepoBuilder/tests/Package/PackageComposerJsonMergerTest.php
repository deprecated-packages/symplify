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
                    'Symplify\MonorepoBuilder\\' => $this->getRelativeSourcePath() . 'src',
                    'Symplify\Statie\\' => $this->getRelativeSourcePath() . 'src',
                ],
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/Source', $expectedJson);
    }

    public function testUniqueRepositories(): void
    {
        $expectedJson = [
            'repositories' => [
                [
                    'type' => 'composer',
                    'url' => 'https://packages.example.org/',
                ],
            ],
            'require' => [
                'php' => '^7.1',
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceUniqueRepositories', $expectedJson);
    }

    private function getRelativeSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/Package/Source/';
    }
}
