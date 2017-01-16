<?php

declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Tests\PhpCsFixer\Factory;

use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symplify\MultiCodingStandard\PhpCsFixer\Factory\FixerFactory;

final class FixerFactoryTest extends TestCase
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp()
    {
        $this->fixerFactory = new FixerFactory();
    }

    /**
     * @dataProvider provideCreateData
     */
    public function testResolveFixerLevels(
        array $fixerLevels,
        array $fixers,
        array $excludedFixers,
        int $expectedFixerCount
    ) {
        $fixers = $this->fixerFactory->createFromLevelsFixersAndExcludedFixers($fixerLevels, $fixers, $excludedFixers);
        $this->assertCount($expectedFixerCount, $fixers);

        if (count($fixers)) {
            $fixer = $fixers[0];
            $this->assertInstanceOf(FixerInterface::class, $fixer);
        }
    }

    public function provideCreateData() : array
    {
        return [
            [[], [], [], 0],
            [[], ['no_whitespace_before_comma_in_array'], [], 1],
            [['psr1'], [], [], 2],
            [['psr2'], [], [], 24],
            [['psr2'], [], ['visibility'],  24],
            [['psr1', 'psr2'], [], [], 26],
            [['psr1', 'psr2'], [], ['visibility'], 26],
        ];
    }
}
