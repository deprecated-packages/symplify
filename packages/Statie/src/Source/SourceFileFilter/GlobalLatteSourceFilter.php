<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Source\SourceFileFilter;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;


final class GlobalLatteSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::GLOBAL_LATTE;
    }

	public function matchesFileSource(SplFileInfo $fileInfo): bool
	{
		if (Strings::contains($fileInfo, '_layouts')) {
			return TRUE;
		}

		if (Strings::contains($fileInfo, '_snippets')) {
			return TRUE;
		}

	    return FALSE;
	}
}
