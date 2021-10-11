<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension\Security;

/**
 * @api
 */
final class AccessControl
{
    /**
     * @var string
     */
    public const PATH = 'path';

    /**
     * @var string
     */
    public const ROLES = 'roles';
}
