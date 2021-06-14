<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Configuration\ModifyingComposerJsonProvider;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AppenderComposerJsonDecorator\AppenderComposerJsonDecoratorTest
 */
final class AppenderComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    public function __construct(
        private ComposerJsonMerger $composerJsonMerger,
        private ModifyingComposerJsonProvider $modifyingComposerJsonProvider
    ) {
    }

    public function decorate(ComposerJson $mainComposerJson): void
    {
        $appendingComposerJson = $this->modifyingComposerJsonProvider->getAppendingComposerJson();
        if (! $appendingComposerJson instanceof ComposerJson) {
            return;
        }

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $appendingComposerJson);
    }
}
