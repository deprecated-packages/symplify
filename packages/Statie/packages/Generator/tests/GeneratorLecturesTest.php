<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

final class GeneratorLecturesTest extends AbstractGeneratorTest
{
    public function testPosts(): void
    {
        $objects = $this->generator->run();
        $this->fileSystemWriter->copyRenderableFiles($objects);

        $this->assertFileExists($this->outputDirectory . '/lecture/open-source-lecture/index.html');
    }
}
