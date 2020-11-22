<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

final class SonarConfigKey
{
    /**
     * @var string
     */
    public const ORGANIZATION = 'sonar.organization';

    /**
     * @var string
     */
    public const PROJECT_KEY = 'sonar.projectKey';

    /**
     * @var string
     */
    public const SOURCES = 'sonar.sources';

    /**
     * @var string
     */
    public const TESTS = 'sonar.tests';
}
