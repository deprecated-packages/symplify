<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Utils\PathNormalizer;

final class RouteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;

    public function __construct(Configuration $configuration, PathNormalizer $pathNormalizer)
    {
        $this->configuration = $configuration;
        $this->pathNormalizer = $pathNormalizer;
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

    private function decorateFile(AbstractFile $file): void
    {
        // manual config override has preference
        if ($file->getOption('outputPath')) {
            $file->setOutputPath((string) $file->getOption('outputPath'));
            $file->setRelativeUrl((string) $file->getOption('outputPath'));
            return;
        }

        // index file
        if ($file->getBaseName() === 'index') {
            $file->setOutputPath('index.html');
            $file->setRelativeUrl('/');
            return;
        }

        // special files
        if (in_array($file->getPrimaryExtension(), ['xml', 'rss', 'json', 'atom', 'css', 'js'], true)) {
            $outputPath = $file->getBaseName();
            // trim file.xml.latte => file.xml
            $outputPath .= in_array($file->getExtension(), ['latte', 'md'], true) ?: '.' . $file->getPrimaryExtension();

            $file->setOutputPath($outputPath);
            $file->setRelativeUrl($outputPath);
            return;
        }

        // fallback
        $relativeDirectory = $this->getRelativeDirectory($file);
        $relativeOutputDirectory = $relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName();
        $outputPath = $relativeOutputDirectory . DIRECTORY_SEPARATOR . 'index.html';

        $file->setOutputPath($outputPath);
        $file->setRelativeUrl($relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName());
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        foreach ($files as $file) {
            $outputPath = $generatorElement->getRoutePrefix() . DIRECTORY_SEPARATOR;

            // if the date is part of file name, it is part of the output path
            if ($file->getDate()) {
                $outputPath .= $file->getDateInFormat('Y') . DIRECTORY_SEPARATOR;
                $outputPath .= $file->getDateInFormat('m') . DIRECTORY_SEPARATOR;
                $outputPath .= $file->getDateInFormat('d') . DIRECTORY_SEPARATOR;
            }

            $outputPath .= $file->getFilenameWithoutDate();
            $outputPath = $this->pathNormalizer->normalize($outputPath);

            $file->setRelativeUrl($outputPath);
            $file->setOutputPath($outputPath . DIRECTORY_SEPARATOR . 'index.html');
        }

        return $files;
    }

    private function getRelativeDirectory(AbstractFile $file): string
    {
        $sourceParts = explode(DIRECTORY_SEPARATOR, $this->configuration->getSourceDirectory());
        $sourceDirectory = array_pop($sourceParts);

        $relativeParts = explode($sourceDirectory, $file->getRelativeDirectory());

        return array_pop($relativeParts);
    }
}
