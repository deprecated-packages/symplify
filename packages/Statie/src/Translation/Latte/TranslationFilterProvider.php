<?php declare(strict_types=1);

namespace Symplify\Statie\Translation\Latte;

use Symfony\Component\Translation\TranslatorInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;
use Symplify\Statie\Translation\MessageAnalyzer;

final class TranslationFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @var string
     */
    public const FILTER_NAME = 'translate';

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
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            self::FILTER_NAME => function (string $message, string $locale) {
                return $this->translate($message, $locale);
            },
        ];
    }

    private function translate(string $message, string $locale): string
    {
        [$domain, $id] = $this->messageAnalyzer->extractDomainFromMessage($message);

        return $this->translator->trans($id, [], $domain, $locale ?: $this->translator->getLocale());
    }
}
