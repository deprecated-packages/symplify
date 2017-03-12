<?php declare(strict_types=1);

namespace Symplify\Statie\Translation;

use Symplify\Statie\Translation\Exception\IncorrectTranslationFormatException;

final class MessageAnalyzer
{
    /**
     * @return string[]
     */
    public function extractDomainFromMessage(string $message): array
    {
        $this->ensureMessageHasCorrectFormat($message);

        [$domain, $message] = explode('.', $message, 2);

        return [$domain, $message];
    }

    private function ensureMessageHasCorrectFormat(string $message): void
    {
        if (strpos($message, '.') === false || strpos($message, ' ')) {
            throw new IncorrectTranslationFormatException(
                sprintf(
                    'Translated text has to be in "group.key" format. "%s" given.',
                    $message
                )
            );
        }
    }
}
