<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class RouteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
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
        if ($file->getBaseName() === 'index') {
            $file->setOutputPath('index.html');
            $file->setRelativeUrl('/');
            return;
        }

        // most of files
//
//        public function matches(AbstractFile $file): bool
//    {
//        return in_array($file->getPrimaryExtension(), ['xml', 'rss', 'json', 'atom', 'css']);
//    }
//
//        public function buildOutputPath(AbstractFile $file): string
//    {
//        if (in_array($file->getExtension(), ['latte', 'md'])) {
//            return $file->getBaseName();
//        }
//
//        return $file->getBaseName() . '.' . $file->getPrimaryExtension();
//
//        public function buildRelativeUrl(AbstractFile $file): string
//    {
//        return $this->buildOutputPath($file);
//    }




        // post, but make globally available for any type of post

        // return PathNormalizer::normalize($this->buildRelativeUrl($file) . '/index.html');

        //
//        $permalink = preg_replace('/:year/', $file->getDateInFormat('Y'), $permalink);
//        $permalink = preg_replace('/:month/', $file->getDateInFormat('m'), $permalink);
//        $permalink = preg_replace('/:day/', $file->getDateInFormat('d'), $permalink);

//        return preg_replace('/:title/', $file->getFilenameWithoutDate(), $permalink);

//        foreach ($this->routes as $route) {
//            if ($route->matches($file)) {
//                $file->setOutputPath($route->buildOutputPath($file));
//                $file->setRelativeUrl($route->buildRelativeUrl($file));
//
//                return;
//            }
//        }

        if (isset($file->getConfiguration()['outputPath'])) {
            $file->setOutputPath($file->getConfiguration()['outputPath']);
            $file->setRelativeUrl($file->getConfiguration()['outputPath']);
        } else {
            $relativeDirectory = $this->getRelativeDirectory($file);
            $relativeOutputDirectory = $relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName();
            $outputPath = $relativeOutputDirectory . DIRECTORY_SEPARATOR . 'index.html';

            $file->setOutputPath($outputPath);
            $file->setRelativeUrl($relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName());
        }
    }

    private function getRelativeDirectory(AbstractFile $file): string
    {
        $sourceParts = explode(DIRECTORY_SEPARATOR, $this->configuration->getSourceDirectory());
        $sourceDirectory = array_pop($sourceParts);

        $relativeParts = explode($sourceDirectory, $file->getRelativeDirectory());

        return array_pop($relativeParts);
    }
}
