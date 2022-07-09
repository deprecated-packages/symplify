<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\ComposerKeyMerger;

use PHPUnit\Framework\TestCase;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\MinimalStabilityKeyMerger;

/**
 * @coversDefaultClass \Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\MinimalStabilityKeyMerger
 */
final class MinimalStabilityKeyMergerTest extends TestCase
{
    /**
     * @dataProvider mergeProvider
     * @covers ::merge
     */
    public function testMerge(
        ComposerJson $mainComposerJson,
        ComposerJson $newComposerJson,
        ?string $expected
    ): void {
        $subject = new MinimalStabilityKeyMerger();
        $subject->merge($mainComposerJson, $newComposerJson);
        self::assertEquals(
            $expected,
            $mainComposerJson->getMinimumStability(),
            'Minimum stability of main Composer JSON does not match expected stability.'
        );
    }

    private static function createPackage(
        ?string $minimumStability
    ): ComposerJson {
        $package = new ComposerJson();

        if ($minimumStability !== null) {
            $package->setMinimumStability($minimumStability);
        }

        return $package;
    }

    public function mergeProvider(): array
    {
        return [
            'Neither has minimum stability' => [
                'mainComposerJson' => self::createPackage(null),
                'newComposerJson' => self::createPackage(null),
                'expected' => null
            ],
            'Only new package has minimum stability' => [
                'mainComposerJson' => self::createPackage(null),
                'newComposerJson' => self::createPackage('rc'),
                'expected' => 'rc'
            ],
            'Only main package has minimum stability' => [
                'mainComposerJson' => self::createPackage('rc'),
                'newComposerJson' => self::createPackage(null),
                'expected' => 'rc'
            ],
            'Wider stability' => [
                'mainComposerJson' => self::createPackage('alpha'),
                'newComposerJson' => self::createPackage('beta'),
                'expected' => 'alpha'
            ],
            'Narrower stability' => [
                'mainComposerJson' => self::createPackage('beta'),
                'newComposerJson' => self::createPackage('alpha'),
                'expected' => 'alpha'
            ]
        ];
    }
}
