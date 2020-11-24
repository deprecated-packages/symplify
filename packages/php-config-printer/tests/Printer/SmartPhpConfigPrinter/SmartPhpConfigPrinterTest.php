<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter;

use Iterator;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Symplify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithConstants;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithType;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\FirstClass;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ValueObject\Simple;
use Symplify\PhpConfigPrinter\ValueObject\Option;

final class SmartPhpConfigPrinterTest extends AbstractKernelTestCase
{
    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(PhpConfigPrinterKernel::class);
        $this->smartPhpConfigPrinter = self::$container->get(SmartPhpConfigPrinter::class);

        $this->configureParameters();
    }

    /**
     * @dataProvider provideData()
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

    private function configureParameters(): void
    {
        /** @var ParameterProvider $parameterProvider */
        $parameterProvider = self::$container->get(ParameterProvider::class);

        $parameterProvider->changeParameter(
            Option::INLINE_VALUE_OBJECT_FUNC_CALL_NAME,
            'Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\custom_inline_object_function'
        );

        $parameterProvider->changeParameter(
            Option::INLINE_VALUE_OBJECTS_FUNC_CALL_NAME,
            'Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\custom_inline_objects_function'
        );
    }
}
