<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Latte;

use Latte\Engine;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
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

    public function decorateFile(AbstractFile $file) : void
    {
        $options = $this->configuration->getGlobalVariables();

        $parameters = $file->getConfiguration() + $options + [
            'posts' => $options['posts'] ?? [],
        ];

        if ($file instanceof PostFile) {
            $parameters['post'] = $file;
        }

        // 1. render inner post content; might be generic to any sub-layouts
        if ($file instanceof PostFile) {
            $this->addTemplateToDynamicLatteStringLoader($file);
            $htmlContent = $this->latteEngine->renderToString($file->getBaseName(), $parameters);
            $file->changeContent($htmlContent);
        }

        // 2. render outer with layout
        $this->prependLayoutToFileContent($file);
        $this->addTemplateToDynamicLatteStringLoader($file);
        $htmlContent = $this->latteEngine->renderToString($file->getBaseName(), $parameters);

        // 3. trim left-over {layout tag}, probably bug-fix
        if ($file instanceof PostFile) {
            $htmlContent = preg_replace('/{layout "[a-z]+"}/', '', $htmlContent);
        }

        $file->changeContent($htmlContent);
    }

    private function addTemplateToDynamicLatteStringLoader(AbstractFile $file) : void
    {
        $this->dynamicStringLoader->addTemplate(
            $file->getBaseName(),
            $file->getContent()
        );
    }

    private function prependLayoutToFileContent(AbstractFile $file) : void
    {
        if (! $file->getLayout()) {
            return;
        }

        $layoutLine = sprintf('{layout "%s"}', $file->getLayout());
        $file->changeContent($layoutLine . PHP_EOL . PHP_EOL . $file->getContent());
    }
}
