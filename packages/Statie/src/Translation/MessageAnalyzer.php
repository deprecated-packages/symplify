<?php declare(strict_types=1);

namespace Symplify\Statie\Translation;

final class MessageAnalyzer
{
    /**
     * @return string[]
     */
    public function extractDomainFromMessage(string $message): array
    {
        $domain = 'messages';
        if (strpos($message, '.') !== false && strpos($message, ' ') === false) {
            [$domain, $message] = explode('.', $message, 2);
        }

        return [$domain, $message];
    }
}
