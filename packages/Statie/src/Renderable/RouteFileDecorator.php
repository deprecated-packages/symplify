<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Utils\PathNormalizer;

final class RouteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;

    public function __construct(StatieConfiguration $statieConfiguration, PathNormalizer $pathNormalizer)
    {
        $this->statieConfiguration = $statieConfiguration;
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

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        foreach ($files as $file) {
            $outputPath = $this->resolveOutputPath($generatorElement, $file);

            $file->setRelativeUrl($outputPath);
            $file->setOutputPath($outputPath . DIRECTORY_SEPARATOR . 'index.html');
        }

        return $files;
    }

    public function getPriority(): int
    {
        return 900;
    }

    private function decorateFile(AbstractFile $file): void
    {
        // manual config override has preference
        $manualOutputPath = $this->getFileOutputPathOption($file);

        if ($manualOutputPath) {
            $file->setOutputPath((string) $manualOutputPath);
            $file->setRelativeUrl((string) $manualOutputPath);
            return;
        }

        if ($this->isRootIndex($file)) {
            $file->setOutputPath('index.html');
            $file->setRelativeUrl('/');
            return;
        }

        // special files
        if (in_array($file->getPrimaryExtension(), ['xml', 'rss', 'json', 'atom', 'css', 'js'], true)) {
            $outputPath = $file->getBaseName();
            // trim file.xml.latte => file.xml
            if ($file->getExtension() !== 'latte') {
                $outputPath .= '.' . $file->getPrimaryExtension();
            }

            $file->setOutputPath($outputPath);
            $file->setRelativeUrl($outputPath);
            return;
        }

        // fallback
        $relativeDirectory = $this->getRelativeDirectory($file);
        $relativeOutputDirectory = $relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName();

        if ($file->getBaseName() === 'index') { // index.* file
            $outputPath = $relativeOutputDirectory . '.html';
        } else {
            $outputPath = $relativeOutputDirectory . DIRECTORY_SEPARATOR . 'index.html';
        }

        $file->setOutputPath($outputPath);
        $file->setRelativeUrl($relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName());
    }

    /**
     * Only if the date is part of file name
     */
    private function prefixWithDateIfFound(AbstractFile $file, string $outputPath): string
    {
        if ($file->getDate() === null) {
            return $outputPath;
        }

        return str_replace(
            [':year', ':month', ':day'],
            [$file->getDateInFormat('Y'), $file->getDateInFormat('m'), $file->getDateInFormat('d')],
            $outputPath
        );
    }

    private function isRootIndex(AbstractFile $file): bool
    {
        return Strings::contains(
            $file->getFilePath(),
            $this->statieConfiguration->getSourceDirectory() . DIRECTORY_SEPARATOR . 'index'
        );
    }

    private function getRelativeDirectory(AbstractFile $file): string
    {
        $sourceParts = explode(DIRECTORY_SEPARATOR, $this->statieConfiguration->getSourceDirectory());
        $sourceDirectory = array_pop($sourceParts);

        $relativeParts = explode($sourceDirectory, $file->getRelativeDirectory());

        return array_pop($relativeParts);
    }

    private function resolveOutputPath(GeneratorElement $generatorElement, AbstractFile $file): string
    {
        /** @var string|null $manualOutputPath */
        $manualOutputPath = $this->getFileOutputPathOption($file);
        if ($manualOutputPath !== null) {
            return $manualOutputPath;
        }

        $outputPath = '';

        if ($generatorElement->getRoutePrefix()) {
            $outputPath .= $generatorElement->getRoutePrefix() . DIRECTORY_SEPARATOR;
        }

        $outputPath = $this->prefixWithDateIfFound($file, $outputPath);
        $outputPath .= $file->getFilenameWithoutDate();

        return $this->pathNormalizer->normalize($outputPath);
    }

    private function getFileOutputPathOption(AbstractFile $file): ?string
    {
        return $file->getOption('outputPath') ?: $file->getOption('output_path');
    }
}
