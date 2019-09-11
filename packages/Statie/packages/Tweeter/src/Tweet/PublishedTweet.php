<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tweet;

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
}
