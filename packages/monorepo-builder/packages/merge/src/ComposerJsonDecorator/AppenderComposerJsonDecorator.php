<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Configuration\ModifyingComposerJsonProvider;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

/**
 * @see \Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AppenderComposerJsonDecorator\AppenderComposerJsonDecoratorTest
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

    public function decorate(ComposerJson $mainComposerJson): void
    {
        $appendingComposerJson = $this->modifyingComposerJsonProvider->getAppendingComposerJson();
        if ($appendingComposerJson === null) {
            return;
        }

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $appendingComposerJson);
    }
}
