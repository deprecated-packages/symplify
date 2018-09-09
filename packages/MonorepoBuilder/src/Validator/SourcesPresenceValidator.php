<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Validator;

use Symplify\MonorepoBuilder\Exception\Validator\InvalidComposerJsonSetupException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;

final class SourcesPresenceValidator
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(ComposerJsonProvider $composerJsonProvider)
    {
        $this->composerJsonProvider = $composerJsonProvider;
    }

    public function validate(): void
    {
        $composerPackageFiles = $this->composerJsonProvider->getPackagesFileInfos();
        if (! count($composerPackageFiles)) {
            throw new InvalidComposerJsonSetupException('No "composer.json" were found in packages.');
        }

        $rootComposerJson = $this->composerJsonProvider->getRootJson();
        if (! isset($rootComposerJson['name'])) {
            throw new InvalidComposerJsonSetupException('Complete "name" to your root "composer.json".');
        }
    }
}
