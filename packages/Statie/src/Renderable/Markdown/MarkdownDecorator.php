<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Renderable\Markdown;

use ParsedownExtra;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class MarkdownDecorator implements DecoratorInterface
{
    /**
     * @var ParsedownExtra
     */
    private $parsedownExtra;

    public function __construct(ParsedownExtra $parsedownExtra)
    {
        $this->parsedownExtra = $parsedownExtra;
    }

    public function decorateFile(AbstractFile $file)
    {
        // skip due to HTML content incompatibility
        if ($file->getExtension() !== 'md') {
            return;
        }

        $htmlContent = $this->parsedownExtra->parse($file->getContent());
        $file->changeContent($htmlContent);
    }
}
