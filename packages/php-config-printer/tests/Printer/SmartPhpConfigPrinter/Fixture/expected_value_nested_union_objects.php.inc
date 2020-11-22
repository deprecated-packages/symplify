<?php

declare(strict_types=1);

use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithType;
use Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use function Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\custom_inline_objects_function;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SecondClass::class)
        ->call('configure', [['some_key' => custom_inline_objects_function([new ClassWithType(new UnionType([new IntegerType(), new StringType()]))])]]);
};
