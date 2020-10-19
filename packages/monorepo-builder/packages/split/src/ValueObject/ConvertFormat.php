<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\ValueObject;

/**
 * @see \Symplify\MonorepoBuilder\Split\Tests\FileSystem\DirectoryToRepositoryProvider\ConvertFormatTest
 */
final class ConvertFormat
{
    /**
     * "PascalCase"
     * ↓
     * "kebab-case"
     *
     * @var string
     */
    public const PASCAL_CASE_TO_KEBAB_CASE = 'pascal_case_to_kebab_case';

    /**
     * @var string
     */
    public const EQUAL = 'equal';
}
