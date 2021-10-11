<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use PhpCsFixer\Fixer\FixerInterface;
use PHPStan\Rules\Rule;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\EasyCodingStandard\Tests\SniffRunner\Application\FixerSource\SomeFile;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

final class PossiblyUnusedClassesFilter
{
    /**
     * @var class-string[]
     */
    private const EXCLUDED_TYPES = [
        ConfigurableRuleInterface::class,
        Rule::class,
        MalformWorkerInterface::class,
        FixerInterface::class,
        BundleInterface::class,
        TestCase::class,
        Command::class,
        SetList::class,
        // part of tests
        SomeFile::class,
    ];

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames): array
    {
        $possiblyUnusedFilesWithClasses = [];

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedNames, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach (self::EXCLUDED_TYPES as $excludedType) {
                if (is_a($fileWithClass->getClassName(), $excludedType, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }
}
