<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\ComposerJson;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerValues;

final class ScopedComposerJsonFactory
{
    /**
     * @var string
     */
    private const SCOPED_PACKAGE_NAME_SUFFIX = '-prefixed';

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(ComposerJsonFactory $composerJsonFactory)
    {
        $this->composerJsonFactory = $composerJsonFactory;
    }

    public function createFromPackageComposerJson(ComposerJson $packageComposeJson): ComposerJson
    {
        $packageName = $packageComposeJson->getName();

        $scopedPackageComposerJson = $this->composerJsonFactory->createEmpty();

        $scopedPackageComposerJson->setName($packageName . self::SCOPED_PACKAGE_NAME_SUFFIX);
        $description = sprintf('Prefixed scoped version of %s package', $packageName);
        $scopedPackageComposerJson->setDescription($description);

        $scopedPackageComposerJson->setBin($packageComposeJson->getBin());

        $license = $scopedPackageComposerJson->getLicense();
        if ($license !== null) {
            $scopedPackageComposerJson->setLicense($license);
        }

        $scopedPackageComposerJson->setRequire($packageComposeJson->getRequirePhp());

        $scopedPackageComposerJson->setConflicting([$packageName]);
        $scopedPackageComposerJson->setReplace([
            $packageName => ComposerValues::SELF_VERSION,
        ]);

        return $scopedPackageComposerJson;
    }
}
