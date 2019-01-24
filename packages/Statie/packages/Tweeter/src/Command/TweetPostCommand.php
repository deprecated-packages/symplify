<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Tweeter\Configuration\Keys;
use Symplify\Statie\Tweeter\Tweet\PostTweet;
use Symplify\Statie\Tweeter\TweetFilter\TweetsFilter;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;
use function Safe\sprintf;

final class TweetPostCommand extends Command
{
    /**
     * @var int
     */
    private $twitterMinimalGapInDays;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var TweetsFilter
     */
    private $tweetsFilter;

    public function __construct(
        int $twitterMinimalGapInDays,
        TwitterApiWrapper $twitterApiWrapper,
        PostTweetsProvider $postTweetsProvider,
        TweetsFilter $tweetsFilter,
        SymfonyStyle $symfonyStyle
    ) {
        $this->twitterMinimalGapInDays = $twitterMinimalGapInDays;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->postTweetsProvider = $postTweetsProvider;
        $this->tweetsFilter = $tweetsFilter;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(sprintf('Publish new tweet from post "%s:" config', Keys::TWEET));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // to soon to tweet after recent tweet
        if ($this->isNewTweetAllowed() === false) {
            return $this->reportTooSoonToTweet();
        }

        $postTweets = $this->postTweetsProvider->provide();
        $postTweets = $this->tweetsFilter->filter($postTweets);

        // no tweetable tweet
        if (count($postTweets) === 0) {
            return $this->reportNoNewTweet();
        }

        /** @var PostTweet $tweet */
        $tweet = array_shift($postTweets);
        $this->tweet($tweet);

        $this->symfonyStyle->success(sprintf('Tweet "%s" was successfully published.', $tweet->getText()));

        return ShellCode::SUCCESS;
    }

    private function isNewTweetAllowed(): bool
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();
        if ($daysSinceLastTweet >= $this->twitterMinimalGapInDays) {
            return true;
        }

        return false;
    }

    private function reportTooSoonToTweet(): int
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();

        $this->symfonyStyle->warning(sprintf(
            'Only %d days passed since last tweet. Minimal gap is %d days, so no tweet until then.',
            $daysSinceLastTweet,
            $this->twitterMinimalGapInDays
        ));

        return ShellCode::SUCCESS;
    }

    private function reportNoNewTweet(): int
    {
        $this->symfonyStyle->warning(sprintf(
            'There is no new tweet to publish. Add a new one to one of your post under "%s:" option.',
            Keys::TWEET
        ));

        return ShellCode::SUCCESS;
    }

    private function tweet(PostTweet $postTweet): void
    {
        if ($postTweet->getImage() !== null) {
            $this->twitterApiWrapper->publishTweetWithImage($postTweet->getText(), $postTweet->getImage());
        } else {
            $this->twitterApiWrapper->publishTweet($postTweet->getText());
        }
    }
}
