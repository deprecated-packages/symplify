<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\File;

use DateTimeInterface;
use Exception;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Utils\PathAnalyzer;

final class PostFile extends File implements \ArrayAccess
{
    /**
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var string
     */
    private $filenameWithoutDate;

    public function __construct(SplFileInfo $fileInfo, string $relativeSource)
    {
        parent::__construct($fileInfo, $relativeSource);
        $this->ensurePathStartsWithDate($fileInfo);

        $this->configuration['date'] = $this->date = PathAnalyzer::detectDate($fileInfo);

        $this->filenameWithoutDate = PathAnalyzer::detectFilenameWithoutDate($fileInfo);
    }

    public function getDate() : DateTimeInterface
    {
        return $this->date;
    }

    public function getDateInFormat(string $format) : string
    {
        return $this->date->format($format);
    }

    public function getFilenameWithoutDate() : string
    {
        return $this->filenameWithoutDate;
    }

    public function offsetGet($offset)
    {
        if ($offset === 'content') {
            return $this->getContent();
        }

        if (!isset($this->configuration[$offset])) {
            throw new \Exception(sprintf(
                'Value "%s" was not found for "%s" object. Available values are "%s"',
                $offset,
                get_class(),
                implode('", "', array_keys($this->configuration))
            ));
        }

        return $this->configuration[$offset];
    }

    public function offsetExists($offset)
    {
        throw new Exception('not supported');
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('not supported');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('not supported');
    }

    private function ensurePathStartsWithDate(SplFileInfo $fileInfo)
    {
        if (! PathAnalyzer::startsWithDate($fileInfo)) {
            throw new Exception(
                'Post name has to start with a date in "Y-m-d" format. E.g. "2016-01-01-name.md".'
            );
        }
    }
}
