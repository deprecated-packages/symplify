<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Routing;

use Symplify\PHP7_Sculpin\Contract\Renderable\DecoratorInterface;
use Symplify\PHP7_Sculpin\Utils\PathNormalizer;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\PostFile;

final class RouteDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $postRoute;

    public function __construct(string $postRoute)
    {
        $this->postRoute = $postRoute;
    }

    public function decorateFile(File $file)
    {
        $file->setOutputPath($this->detectFileOutputPath($file));
    }

    private function detectFileOutputPath(File $file) : string
    {
        if ($this->isFileIndex($file)) {
            return 'index.html';
        }

        if ($this->isPostFile($file)) {
            /* @var PostFile $file */
            return $this->createOutputPathForPostFile($file, $this->postRoute);
        }

        if ($this->isFileNonHtml($file)) {
            return $file->getBaseName().'.'.$file->getPrimaryExtension();
        }

        return $file->getBaseName().DIRECTORY_SEPARATOR.'index.html';
    }

    private function isFileIndex(File $file) : bool
    {
        return $file->getBaseName() === 'index';
    }

    private function isFileNonHtml(File $file) : bool
    {
        return in_array(
            $file->getPrimaryExtension(),
            ['xml', 'rss', 'json', 'atom', 'css']
        );
    }

    private function isPostFile(File $file) : bool
    {
        return $file instanceof PostFile;
    }

    private function createOutputPathForPostFile(PostFile $file, string $postRoute) : string
    {
        $permalink = $postRoute;
        $permalink = preg_replace('/:year/', $file->getDateInFormat('Y'), $permalink);
        $permalink = preg_replace('/:month/', $file->getDateInFormat('m'), $permalink);
        $permalink = preg_replace('/:day/', $file->getDateInFormat('j'), $permalink);
        $permalink = preg_replace('/:filename/', $file->getFilenameWithoutDate(), $permalink);
        $permalink = preg_replace('/:title/', $file->getFilenameWithoutDate(), $permalink);
        $permalink .= '/index.html';

        return PathNormalizer::normalize($permalink);
    }
}
