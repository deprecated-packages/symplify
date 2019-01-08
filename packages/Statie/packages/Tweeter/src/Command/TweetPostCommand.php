<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Exception\Configuration\ConfigurationException;
use Symplify\Statie\Tweeter\Configuration\Keys;
use Symplify\Statie\Tweeter\Tweet\Tweet;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;
use Symplify\Statie\Tweeter\TweetProvider\UnpublishedTweetsResolver;
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
     * @var UnpublishedTweetsResolver
     */
    private $unpublishedTweetsResolver;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    public function __construct(
        int $twitterMinimalGapInDays,
        TwitterApiWrapper $twitterApiWrapper,
        PostTweetsProvider $postTweetsProvider,
        UnpublishedTweetsResolver $unpublishedTweetsResolver,
        SymfonyStyle $symfonyStyle
    ) {
        $this->twitterMinimalGapInDays = $twitterMinimalGapInDays;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->postTweetsProvider = $postTweetsProvider;
        $this->unpublishedTweetsResolver = $unpublishedTweetsResolver;
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
        $this->ensureNewTweetIsAllowed();

        $tweetsToPublish = $this->unpublishedTweetsResolver->excludePublishedTweets(
            $this->postTweetsProvider->provide(),
            $this->twitterApiWrapper->getPublishedTweets()
        );

        if (! count($tweetsToPublish)) {
            $this->symfonyStyle->warning(sprintf(
                'There is no new tweet to publish. Add a new one to one of your post under "%s:" option.',
                Keys::TWEET
            ));

            return ShellCode::SUCCESS;
        }

        /** @var Tweet $tweet */
        $tweet = array_shift($tweetsToPublish);
        $this->tweet($tweet);

        $this->symfonyStyle->success(sprintf('Tweet "%s" was successfully published.', $tweet->getText()));

        return ShellCode::SUCCESS;
    }

    private function ensureNewTweetIsAllowed(): void
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();
        if ($daysSinceLastTweet >= $this->twitterMinimalGapInDays) {
            return;
        }

        throw new ConfigurationException(sprintf(
            'Only %d days passed since last tweet. Minimal gap is %d days, so no tweet until then.',
            $daysSinceLastTweet,
            $this->twitterMinimalGapInDays
        ));
    }

    private function tweet(Tweet $tweet): void
    {
        if ($tweet->getImage() !== null) {
            $this->twitterApiWrapper->publishTweetWithImage($tweet->getText(), $tweet->getImage());
        } else {
            $this->twitterApiWrapper->publishTweet($tweet->getText());
        }
    }
}
