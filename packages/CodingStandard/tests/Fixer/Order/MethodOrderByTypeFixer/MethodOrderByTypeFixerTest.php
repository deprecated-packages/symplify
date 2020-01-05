<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer;

use Iterator;
use PhpCsFixer\Fixer\FixerInterface as PhpCsFixerFixerInterface;
use Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer;
use Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Source\FixerInterface;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class MethodOrderByTypeFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->autoloadTestFixture = true;

        $this->doTestFiles([$file]);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/FixerWithAbstractParent.php'];
        yield [__DIR__ . '/Fixture/SomeFixer.php.inc'];
        yield [__DIR__ . '/Fixture/RealFixer.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return MethodOrderByTypeFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'method_order_by_type' => [
                FixerInterface::class => ['firstMethod', 'secondMethod'],
                PhpCsFixerFixerInterface::class => ['firstMethod', 'secondMethod', 'getDefinition', 'isCandidate'],
            ],
        ];
    }
}
