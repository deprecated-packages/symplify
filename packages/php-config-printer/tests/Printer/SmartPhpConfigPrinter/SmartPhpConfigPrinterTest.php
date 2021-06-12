<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter;

use Iterator;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Symplify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithConstants;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithType;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\FirstClass;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ValueObject\Simple;

final class SmartPhpConfigPrinterTest extends AbstractKernelTestCase
{
    private SmartPhpConfigPrinter $smartPhpConfigPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(PhpConfigPrinterKernel::class);
        $this->smartPhpConfigPrinter = $this->getService(SmartPhpConfigPrinter::class);
    }

    /**
     * @dataProvider provideData()
     * @param array<string, array<string, string>>|array<string, null>|array<string, array<int|string, string>>|array<string, array<string, Simple>>|array<string, array<string, Simple[]>>|array<string, array<string, ClassWithType[]>> $services
     */
    public function test(array $services, string $expectedContentFilePath): void
    {
        $printedContent = $this->smartPhpConfigPrinter->printConfiguredServices($services);
        $this->assertStringEqualsFile($expectedContentFilePath, $printedContent, $expectedContentFilePath);
    }

    public function provideData(): Iterator
    {
        yield [[
            FirstClass::class => [
                'some_key' => 'some_value',
            ],
            SecondClass::class => null,
        ], __DIR__ . '/Fixture/expected_file.php.inc'];

        yield [[
            ClassWithConstants::class => [
                ClassWithConstants::CONFIG_KEY => 'it is constant',
                ClassWithConstants::NUMERIC_CONFIG_KEY => 'a lot of numbers',
            ],
        ], __DIR__ . '/Fixture/expected_constant_file.php.inc'];

        yield [[
            SecondClass::class => [
                'some_key' => new Simple('Steve'),
            ],
        ], __DIR__ . '/Fixture/expected_value_object_file.php.inc'];

        yield [[
            SecondClass::class => [
                'some_key' => [new Simple('Paul')],
            ],
        ], __DIR__ . '/Fixture/expected_value_objects_file.php.inc'];

        yield [[
            SecondClass::class => [
                'some_key' => [new ClassWithType(new StringType())],
            ],
        ], __DIR__ . '/Fixture/expected_value_nested_objects.php.inc'];

        $unionType = new UnionType([new StringType(), new IntegerType()]);

        yield [[
            SecondClass::class => [
                'some_key' => [new ClassWithType($unionType)],
            ],
        ], __DIR__ . '/Fixture/expected_value_nested_union_objects.php.inc'];
    }
}
