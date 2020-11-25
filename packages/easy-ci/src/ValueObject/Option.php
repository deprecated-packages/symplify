<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

final class Option
{
    /**
     * @api
     * @var string
     */
    public const SONAR_ORGANIZATION = 'sonar_organization';

    /**
     * @api
     * @var string
     */
    public const SONAR_PROJECT_KEY = 'sonar_project_key';

    /**
     * @api
     * @var string
     */
    public const SONAR_DIRECTORIES = 'sonar_directories';

    /**
     * @api
     * @var string
     */
    public const SONAR_OTHER_PARAMETERS = 'sonar_other_parameters';
}
