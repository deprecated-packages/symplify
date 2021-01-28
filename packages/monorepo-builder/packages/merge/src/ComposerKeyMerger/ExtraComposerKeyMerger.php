<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class ExtraComposerKeyMerger implements ComposerKeyMergerInterface
{
    /**
     * @var string
     */
    private const PHPSTAN = 'phpstan';

    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    public function __construct(ParametersMerger $parametersMerger)
    {
        $this->parametersMerger = $parametersMerger;
    }

    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getExtra() === []) {
            return;
        }

        // clean content not desired to merge
        $newComposerJsonExtra = $newComposerJson->getExtra();
        // part of the plugin only
        if (isset($newComposerJsonExtra[self::PHPSTAN]['includes'])) {
            unset($newComposerJsonExtra[self::PHPSTAN]['includes']);

            if ($newComposerJsonExtra[self::PHPSTAN] === []) {
                unset($newComposerJsonExtra[self::PHPSTAN]);
            }
        }

        $extra = $this->parametersMerger->mergeWithCombine($mainComposerJson->getExtra(), $newComposerJsonExtra);

        // do not merge extra alias as only for local packages
        if (isset($extra['branch-alias'])) {
            unset($extra['branch-alias']);
        }

        if (! is_array($extra)) {
            return;
        }

        $mainComposerJson->setExtra($extra);
    }
}
