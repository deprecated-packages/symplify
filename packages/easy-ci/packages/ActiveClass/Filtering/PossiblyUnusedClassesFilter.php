<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPES_TO_SKIP = [
        'Symfony\Bundle\FrameworkBundle\Controller\Controller',
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'Symfony\Component\HttpKernel\Bundle\BundleInterface',
        'Symfony\Component\HttpKernel\KernelInterface',
        'Symfony\Component\Console\Command\Command',
        'Twig\Extension\ExtensionInterface',
        'PhpCsFixer\Fixer\FixerInterface',
        'PHPUnit\Framework\TestCase',
        'PHPStan\Rules\Rule',
        'PHPStan\Command\ErrorFormatter\ErrorFormatter',
    ];

    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames): array
    {
        $possiblyUnusedFilesWithClasses = [];

        $typesToSkip = $this->parameterProvider->provideArrayParameter(Option::TYPES_TO_SKIP);

        $typesToSkip = array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedNames, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach ($typesToSkip as $typeToSkip) {
                if ($this->isClassSkipped($fileWithClass, $typeToSkip)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }

    private function isClassSkipped(FileWithClass $fileWithClass, string $typeToSkip): bool
    {
        if (! str_contains($typeToSkip, '*')) {
            return is_a($fileWithClass->getClassName(), $typeToSkip, true);
        }

        // try fnmatch
        return fnmatch($typeToSkip, $fileWithClass->getClassName(), FNM_NOESCAPE);
    }
}
