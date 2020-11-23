<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\ValueObjectFactory;

use Nette\Utils\Strings;
use Symplify\Psr4Switcher\Configuration\Psr4SwitcherConfiguration;
use Symplify\Psr4Switcher\Utils\MigrifyStrings;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPath;

final class Psr4NamespaceToPathFactory
{
    /**
     * @var MigrifyStrings
     */
    private $migrifyStrings;

    /**
     * @var Psr4SwitcherConfiguration
     */
    private $psr4SwitcherConfiguration;

    public function __construct(MigrifyStrings $migrifyStrings, Psr4SwitcherConfiguration $psr4SwitcherConfiguration)
    {
        $this->migrifyStrings = $migrifyStrings;
        $this->psr4SwitcherConfiguration = $psr4SwitcherConfiguration;
    }

    public function createFromClassAndFile(string $class, string $file): ?Psr4NamespaceToPath
    {
        $sharedSuffix = $this->migrifyStrings->findSharedSlashedSuffix([$class . '.php', $file]);

        $uniqueFilePath = $this->migrifyStrings->subtractFromRight($file, $sharedSuffix);
        $uniqueNamespace = $this->migrifyStrings->subtractFromRight($class . '.php', $sharedSuffix);

        // fallback for identical namespace + file directory
        if ($uniqueNamespace === '') {
            // shorten shared suffix by "Element/"
            $sharedSuffix = '/' . Strings::after($sharedSuffix, '/');

            $uniqueFilePath = $this->migrifyStrings->subtractFromRight($file, $sharedSuffix);
            $uniqueNamespace = $this->migrifyStrings->subtractFromRight($class . '.php', $sharedSuffix);
        }

        $commonFilePathPrefix = Strings::findPrefix(
            [$uniqueFilePath, $this->psr4SwitcherConfiguration->getComposerJsonPath()]
        );

        $uniqueNamespace = rtrim($uniqueNamespace, '\\');

        $relativeDirectory = $this->migrifyStrings->subtractFromLeft($uniqueFilePath, $commonFilePathPrefix);

        $relativeDirectory = rtrim($relativeDirectory, '/');

        if ($uniqueNamespace === '' || $relativeDirectory === '') {
            // skip
            return null;
        }

        return new Psr4NamespaceToPath($uniqueNamespace, $relativeDirectory);
    }
}
