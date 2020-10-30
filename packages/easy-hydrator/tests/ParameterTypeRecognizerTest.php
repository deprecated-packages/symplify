<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use ReflectionClass;
use ReflectionMethod;
use Symplify\EasyHydrator\ParameterTypeRecognizer;
use Symplify\EasyHydrator\Tests\Fixture\DocTypeTestObject;
use Symplify\EasyHydrator\Tests\Fixture\Person;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

class ParameterTypeRecognizerTest extends AbstractKernelTestCase
{
    /**
     * @var ParameterTypeRecognizer
     */
    private $parameterTypeRecognizer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyHydratorTestKernel::class);

        $this->parameterTypeRecognizer = self::$container->get(ParameterTypeRecognizer::class);
    }

    public function test(): void
    {
        $reflectionClass = new ReflectionClass(DocTypeTestObject::class);

        /** @var ReflectionMethod $reflectionConstructor */
        $reflectionConstructor = $reflectionClass->getConstructor();
        $reflectionParameters = $reflectionConstructor->getParameters();

        for ($i = 0; $i < 6; ++$i) {
            $actual = $this->parameterTypeRecognizer->getTypeFromDocBlock($reflectionParameters[$i]);
            $this->assertSame('string', $actual);
        }

        for ($i = 6; $i < 12; ++$i) {
            $actual = $this->parameterTypeRecognizer->getTypeFromDocBlock($reflectionParameters[$i]);
            $this->assertSame(Person::class, $actual);
        }
    }
}
