<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use SplFileInfo;
use Symplify\Statie\Amp\AmpLinkDecorator;
use Symplify\Statie\Amp\HtmlToAmpConvertor;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Output\FileSystemWriter;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Renderable\Latte\LatteDecorator;
use Symplify\Statie\Renderable\Markdown\MarkdownDecorator;
use Symplify\Statie\Renderable\Routing\RouteDecorator;

final class RenderableFilesProcessor
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    /**
     * @var MarkdownDecorator
     */
    private $markdownDecorator;

    /**
     * @var LatteDecorator
     */
    private $latteDecorator;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HtmlToAmpConvertor
     */
    private $htmlToAmpConvertor;

    /**
     * @var AmpLinkDecorator
     */
    private $ampLinkDecorator;

    public function __construct(
        FileFactory $fileFactory,
        RouteDecorator $routeDecorator,
        ConfigurationDecorator $configurationDecorator,
        MarkdownDecorator $markdownDecorator,
        LatteDecorator $latteDecorator,
        FileSystemWriter $fileSystemWriter,
        Configuration $configuration,
        HtmlToAmpConvertor $htmlAmpConvertor,
        AmpLinkDecorator $ampLinkDecorator
    ) {
        $this->fileFactory = $fileFactory;
        $this->routeDecorator = $routeDecorator;
        $this->configurationDecorator = $configurationDecorator;
        $this->markdownDecorator = $markdownDecorator;
        $this->latteDecorator = $latteDecorator;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->configuration = $configuration;
        $this->htmlToAmpConvertor = $htmlAmpConvertor;
        $this->ampLinkDecorator = $ampLinkDecorator;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function processFiles(array $fileInfos): void
    {
        if (! count($fileInfos)) {
            return;
        }

        $files = $this->createFileObjectsFromFileInfos($fileInfos);

        $this->setPostsToConfiguration($files);
        $this->setRoutesToFiles($files);

        $this->setFileConfigurationToFile($files);
        $this->formatFileContentFromMarkdownToHtml($files);
        $this->formatFileContentFromLatteToHtml($files);

        if (isset($this->configuration->getOptions()['amp']) && $this->configuration->getOptions()['amp']) {
            $ampFiles = $this->cloneArray($files);
            $this->formatFileContentWithAmpLink($files);
            $this->fileSystemWriter->copyRenderableFiles($files);

            $ampFiles = $this->createAmpVersions($ampFiles);
            $this->fileSystemWriter->copyRenderableFiles($ampFiles);
        } else {
            $this->fileSystemWriter->copyRenderableFiles($files);
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return File[]
     */
    private function createFileObjectsFromFileInfos(array $fileInfos): array
    {
        $files = [];
        foreach ($fileInfos as $id => $fileInfo) {
            $files[$id] = $this->fileFactory->create($fileInfo);
        }

        return $files;
    }

    /**
     * @param File[] $files
     */
    private function setPostsToConfiguration(array $files): void
    {
        if (reset($files) instanceof PostFile) {
            $this->configuration->addGlobalVarialbe('posts', $files);
        }
    }

    /**
     * @param File[] $files
     */
    private function setRoutesToFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->routeDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function setFileConfigurationToFile(array $files): void
    {
        foreach ($files as $file) {
            $this->configurationDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function formatFileContentFromMarkdownToHtml(array $files): void
    {
        foreach ($files as $file) {
            $this->markdownDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function formatFileContentFromLatteToHtml(array $files): void
    {
        foreach ($files as $file) {
            $this->latteDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     * @return File[] $files
     */
    private function formatFileContentWithAmpLink(array $files): array
    {
        foreach ($files as $file) {
            $this->ampLinkDecorator->decorateFile($file);
        }

        return $files;
    }

    /**
     * @param File[] $files
     * @return File[] $files
     */
    private function createAmpVersions(array $files): array
    {
        foreach ($files as $file) {
            $baseUrl = $this->configuration->getOptions()['baseUrl'] ?? '';
            $originalUrl = $baseUrl . $file->getOutputPath();

            $amp = $this->htmlToAmpConvertor->convert($file->getContent(), $originalUrl);
            $file->changeContent($amp);

            $file->setOutputPath('/amp/' . $file->getOutputPath());
        }

        return $files;
    }

    /**
     * @param File[] $files
     * @return File[]
     */
    private function cloneArray(array $files): array
    {
        $clonedFiles = [];
        foreach ($files as $key => $file) {
            $clonedFiles[$key] = clone $file;
        }

        return $clonedFiles;
    }
}
