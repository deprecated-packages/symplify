<?php declare(strict_types=1);

namespace Symplify\Statie\Translation\Latte;

use Symfony\Component\Translation\TranslatorInterface;
use Symplify\Statie\Translation\MessageAnalyzer;
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
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'translate' => function (string $message, string $locale) {
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
