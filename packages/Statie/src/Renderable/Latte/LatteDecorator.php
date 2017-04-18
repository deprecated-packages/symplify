<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Latte;

use Latte\Engine;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;

final class LatteDecorator implements DecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    public function __construct(
        Configuration $configuration,
        Engine $latteEngine,
        DynamicStringLoader $dynamicStringLoader
    ) {
        $this->configuration = $configuration;
        $this->latteEngine = $latteEngine;
        $this->dynamicStringLoader = $dynamicStringLoader;
    }

    public function decorateFile(AbstractFile $file): void
    {
        $options = $this->configuration->getOptions();

        $parameters = $file->getConfiguration() + $options + [
            'posts' => $options['posts'] ?? [],
            'file' => $file,
        ];

        if ($file instanceof PostFile) {
            $parameters['post'] = $file;
        }

        $this->renderInnerPostContent($file, $parameters);

        $htmlContent = $this->renderOuterWithLayout($file, $parameters);
        $htmlContent = $this->trimLeftOverLayoutTag($file, $htmlContent);

        $file->changeContent($htmlContent);
    }

    private function addTemplateToDynamicLatteStringLoader(AbstractFile $file): void
    {
        $this->dynamicStringLoader->changeContent(
            $file->getBaseName(),
            $file->getContent()
        );
    }

    private function prependLayoutToFileContent(AbstractFile $file): void
    {
        if (! $file->getLayout()) {
            return;
        }

        $layoutLine = sprintf('{layout "%s"}', $file->getLayout());
        $file->changeContent($layoutLine . PHP_EOL . PHP_EOL . $file->getContent());
    }

    /**
     * @param AbstractFile $file
     * @param mixed[] $parameters
     */
    private function renderInnerPostContent(AbstractFile $file, array $parameters): void
    {
        if ($file instanceof PostFile) {
            $this->addTemplateToDynamicLatteStringLoader($file);
            $htmlContent = $this->latteEngine->renderToString($file->getBaseName(), $parameters);
            $file->changeContent($htmlContent);
        }
    }

    /**
     * @param AbstractFile $file
     * @param mixed[] $parameters
     */
    private function renderOuterWithLayout(AbstractFile $file, array $parameters): string
    {
        $this->prependLayoutToFileContent($file);
        $this->addTemplateToDynamicLatteStringLoader($file);

        return $this->latteEngine->renderToString($file->getBaseName(), $parameters);
    }

    private function trimLeftOverLayoutTag(AbstractFile $file, string $htmlContent): string
    {
        if ($file instanceof PostFile) {
            return preg_replace('/{layout "[a-z]+"}/', '', $htmlContent);
        }

        return $htmlContent;
    }
}
