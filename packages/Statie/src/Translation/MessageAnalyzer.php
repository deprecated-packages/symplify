<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Translation;

final class MessageAnalyzer
{
    public function extractDomainFromMessage(string $message) : array
    {
        if (strpos($message, '.') !== false && strpos($message, ' ') === false) {
            list($domain, $message) = explode('.', $message, 2);
        } else {
            $domain = 'messages';
        }

        return [$domain, $message];
    }
}
