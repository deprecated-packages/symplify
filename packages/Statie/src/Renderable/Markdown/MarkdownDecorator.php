<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Renderable\Markdown;

use Nette\Utils\Strings;
use ParsedownExtra;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;
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
        $htmlContent = $this->decorateHeadlinesWithTocAnchors($htmlContent);
        $file->changeContent($htmlContent);
    }

    private function decorateHeadlinesWithTocAnchors(string $htmlContent) : string
    {
        return Regex::replace('/<h([1-6])>(.*?)<\/h([1-6])>/', function (MatchResult $result) {
            return sprintf(
                '<h%s id="%s">%s</h%s>',
                $result->group(1),
                Strings::webalize($result->group(2)),
                $result->group(2),
                $result->group(1)
            );
        }, $htmlContent)->result();
    }
}
