<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests\Configuration;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Generator\Tests\AbstractGeneratorTest;
use Symplify\Statie\Generator\Tests\Configuration\GeneratorElementGuardSource\InvalidLectureSorter;
use Symplify\Statie\HttpKernel\StatieKernel;

final class GeneratorElementGuardForObjectSorterTest extends AbstractGeneratorTest
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            StatieKernel::class,
            [__DIR__ . '/GeneratorElementGuardSource/config-invalid-object-sorter.yml']
        );

        parent::setUp();
    }

    public function testExceptionOnInvalidObjectSorter(): void
    {
        $this->expectException(InvalidGeneratorElementDefinitionException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value in "object_sorter" must extend "%s". "%s" type given In "parameters > generators > lectures".',
                ObjectSorterInterface::class,
                InvalidLectureSorter::class
            )
        );

        $this->generator->run();
    }
}
