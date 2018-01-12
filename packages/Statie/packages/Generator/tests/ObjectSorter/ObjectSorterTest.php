<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests\ObjectSorter;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Generator\Tests\AbstractGeneratorTest;
use Symplify\Statie\Generator\Tests\ObjectSorter\ObjectSorterSource\InvalidLectureSorter;

final class ObjectSorterTest extends AbstractGeneratorTest
{
    public function testExceptionOnInvalidObjectSorter(): void
    {
        $this->expectException(InvalidGeneratorElementDefinitionException::class);
        $this->expectExceptionMessage(sprintf(
            'Value in "object_sorter" must extend "%s". "%s" type given In "parameters > generators > lectures".',
            ObjectSorterInterface::class,
            InvalidLectureSorter::class
        ));

        $this->generator->run();
    }

    protected function getConfig(): string
    {
        return __DIR__ . '/ObjectSorterSource/config-invalid-object-sorter.yml';
    }
}
