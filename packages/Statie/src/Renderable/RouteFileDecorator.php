<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\File\AbstractFile;

final class RouteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Configuration $configuration, Generator $generator)
    {
        $this->configuration = $configuration;
        $this->generator = $generator;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array
    {
        foreach ($files as $file) {
            $this->decorateFile($file);
        }

        return $files;
    }



    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        dump($files, $generatorElement);

        // post, but make globally available for any type of post
        // return PathNormalizer::normalize($this->buildRelativeUrl($file) . '/index.html');
        //
//        $permalink = preg_replace('/:year/', $file->getDateInFormat('Y'), $permalink);
//        $permalink = preg_replace('/:month/', $file->getDateInFormat('m'), $permalink);
//        $permalink = preg_replace('/:day/', $file->getDateInFormat('d'), $permalink);

//        return preg_replace('/:title/', $file->getFilenameWithoutDate(), $permalink);

        // TODO: Implement decorateFilesWithGeneratorElement() method.
    }

    private function decorateFile(AbstractFile $file): void
    {
        // manual config override has preference
        if (isset($file->getConfiguration()['outputPath'])) {
            $file->setOutputPath($file->getConfiguration()['outputPath']);
            $file->setRelativeUrl($file->getConfiguration()['outputPath']);
            return;
        }

        // index file
        if ($file->getBaseName() === 'index') {
            $file->setOutputPath('index.html');
            $file->setRelativeUrl('/');
            return;
        }

        // special files
        if (in_array($file->getPrimaryExtension(), ['xml', 'rss', 'json', 'atom'], true)) {
            $outputPath = $file->getBaseName();
            // trim file.xml.latte => file.xml
            $outputPath .= in_array($file->getExtension(), ['latte', 'md'], true) ?: '.' . $file->getPrimaryExtension();

            $file->setOutputPath($outputPath);
            $file->setRelativeUrl($outputPath);
            return;
        }

        $relativeDirectory = $this->getRelativeDirectory($file);
        $relativeOutputDirectory = $relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName();
        $outputPath = $relativeOutputDirectory . DIRECTORY_SEPARATOR . 'index.html';

        $file->setOutputPath($outputPath);
        $file->setRelativeUrl($relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName());
    }

    private function getRelativeDirectory(AbstractFile $file): string
    {
        $sourceParts = explode(DIRECTORY_SEPARATOR, $this->configuration->getSourceDirectory());
        $sourceDirectory = array_pop($sourceParts);

        $relativeParts = explode($sourceDirectory, $file->getRelativeDirectory());

        return array_pop($relativeParts);
    }
}
