<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Translation\Latte;

use Symfony\Component\Translation\TranslatorInterface;
use Symplify\PHP7_Sculpin\Translation\MessageAnalyzer;
use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class TranslationFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var MessageAnalyzer
     */
    private $messageAnalyzer;

    public function __construct(TranslatorInterface $translator, MessageAnalyzer $messageAnalyzer)
    {
        $this->translator = $translator;
        $this->messageAnalyzer = $messageAnalyzer;
    }

    /**
     * @return array <string=>callback>
     */
    public function getFilters() : array
    {
        return [
            'translate' => function (string $message, string $locale) {
                return $this->translate($message, $locale);
            }
        ];
    }

    private function translate(string $message, string $locale) : string
    {
        list($domain, $id) = $this->messageAnalyzer->extractDomainFromMessage($message);

        return $this->translator->trans($id, [], $domain, $locale ?: $this->translator->getLocale());
    }
}
