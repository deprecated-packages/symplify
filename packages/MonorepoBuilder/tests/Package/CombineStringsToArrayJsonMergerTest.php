<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testSharedNamespaces(): void
    {
        $expectedJson = [
            'autoload' => [
                'psr-4' => [
                    'ACME\Another\\' => 'packages/A',
                    'ACME\Model\Core\\' => ['packages/A', 'packages/B'],
                    'ACME\\YetAnother\\' => ['packages/A'],
                    'ACME\\YetYetAnother\\' => 'packages/A',
                ],
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceAutoloadSharedNamespaces', $expectedJson);
    }
}
