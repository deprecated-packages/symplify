<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests\Configuration;

use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Generator\Tests\AbstractGeneratorTest;
use Symplify\Statie\Generator\Tests\Configuration\GeneratorElementGuardSource\InvalidObject;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GeneratorElementGuardForObjectTest extends AbstractGeneratorTest
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            StatieKernel::class,
            [__DIR__ . '/GeneratorElementGuardSource/config-invalid-object.yml']
        );

        parent::setUp();
    }

    public function testExceptionOnInvalidObject(): void
    {
        $this->expectException(InvalidGeneratorElementDefinitionException::class);

        $this->expectExceptionMessage(
            sprintf(
                'Value in "object" must extend "%s". "%s" type given In "parameters > generators > lectures".',
                AbstractFile::class,
                InvalidObject::class
            )
        );

        $this->generator->run();
    }
}
