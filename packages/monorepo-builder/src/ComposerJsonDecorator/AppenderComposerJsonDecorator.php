<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonMerger;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Configuration\ModifyingComposerJsonProvider;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

/**
 * @see \Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AppenderComposerJsonDecorator\AppenderComposerJsonDecoratorTest
 */
final class AppenderComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    /**
     * @var ModifyingComposerJsonProvider
     */
    private $modifyingComposerJsonProvider;

    public function __construct(
        ComposerJsonMerger $composerJsonMerger,
        ModifyingComposerJsonProvider $modifyingComposerJsonProvider
    ) {
        $this->composerJsonMerger = $composerJsonMerger;
        $this->modifyingComposerJsonProvider = $modifyingComposerJsonProvider;
    }

    public function decorate(ComposerJson $rootComposerJson): void
    {
        $appendingComposerJson = $this->modifyingComposerJsonProvider->getAppendingComposerJson();
        if ($appendingComposerJson === null) {
            return;
        }

        $this->composerJsonMerger->mergeJsonToRoot($appendingComposerJson, $rootComposerJson);
    }
}
