<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Markdown;

use Michelf\MarkdownExtra;
use Symplify\PHP7_Sculpin\Contract\Renderable\DecoratorInterface;
use Symplify\PHP7_Sculpin\Renderable\File\File;

final class MarkdownDecorator implements DecoratorInterface
{
    /**
     * @var MarkdownExtra
     */
    private $markdownExtra;

    public function __construct(MarkdownExtra $markdown)
    {
        $this->markdownExtra = $markdown;
    }

    public function decorateFile(File $file)
    {
        // skip due to HTML content incompatibility
        if ($file->getExtension() !== 'md') {
            return;
        }

        $htmlContent = $this->markdownExtra->transform($file->getContent());
        $file->changeContent($htmlContent);
    }
}
