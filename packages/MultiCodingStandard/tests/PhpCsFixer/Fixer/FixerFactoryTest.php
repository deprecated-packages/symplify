<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Tests\PhpCsFixer\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symplify\MultiCodingStandard\PhpCsFixer\Fixer\FixerFactory;
use Symplify\MultiCodingStandard\Tests\ContainerFactory;

final class FixerFactoryTest extends TestCase
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->fixerFactory = $container->getByType(FixerFactory::class);
    }

    /**
     * @dataProvider provideCreateData
     */
    public function testResolveFixerLevels(array $rules, array $excludedRules, int $expectedFixerCount)
    {
        $rules = $this->fixerFactory->createFromRulesAndExcludedRules($rules, $excludedRules);
        $this->assertCount($expectedFixerCount, $rules);

        if (count($rules)) {
            $fixer = $rules[0];
            $this->assertInstanceOf(FixerInterface::class, $fixer);
        }
    }

    public function provideCreateData() : array
    {
        return [
            [[], [], 0],
            [['no_whitespace_before_comma_in_array'], [], 1],
            [['@PSR1'], [], 2],
            [['@PSR2'], [], 24],
            [['@PSR2', 'whitespace_after_comma_in_array'], [], 25],
            [['@PSR1', '@PSR2'], [], 24],
            [['@PSR1', '@PSR2'], ['visibility'], 24],
        ];
    }
}
