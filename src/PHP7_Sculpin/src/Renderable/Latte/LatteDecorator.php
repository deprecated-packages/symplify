<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Latte;

use Latte\Engine;
use Nette\Utils\Strings;
use Symplify\PHP7_Sculpin\Contract\Renderable\DecoratorInterface;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Renderable\File\File;

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

    public function decorateFile(File $file)
    {
        $options = $this->configuration->getOptions();

        $parameters = $file->getConfiguration() + $options + [
            'posts' => $options['posts'] ?? [],
        ];

        $this->prependLayoutToFileContent($file);
        $this->addTemplateToDynamicLatteStringLoader($file);

        $htmlContent = $this->latteEngine->renderToString($file->getBaseName(), $parameters);
        $file->changeContent($htmlContent);
    }

    private function addTemplateToDynamicLatteStringLoader(File $file)
    {
        $this->dynamicStringLoader->addTemplate($file->getBaseName(), $file->getContent());
    }

    private function prependLayoutToFileContent(File $file)
    {
        if (!$file->getLayout()) {
            return;
        }

        if (Strings::startsWith($file->getContent(), '{layout')) {
            return;
        }

        $layoutLine = sprintf('{layout "%s"}', $file->getLayout());
        $file->changeContent($layoutLine.$file->getContent());
    }
}
