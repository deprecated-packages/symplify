<?php
declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\ValueObject;

final class ComposerJsonSection
{
    /**
     * @var string
     */
    public const REPOSITORIES = 'repositories';

    /**
     * @var string
     */
    public const REQUIRE_DEV = 'require-dev';

    /**
     * @var string
     */
    public const REQUIRE = 'require';
}
