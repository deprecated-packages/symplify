<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tweet;

use Nette\Utils\Strings;

final class PublishedTweet
{
    /**
     * @var string
     */
    private $text;

    public function __construct(string $text)
    {
        $this->text = htmlspecialchars_decode($text);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isSimilarTo(self $anotherTweet): bool
    {
        return Strings::startsWith(
            $this->text,
            // published tweet is usually modified by Twitter API, so we just use starting part of it
            Strings::substring($anotherTweet->getText(), 0, 50)
        );
    }
}
