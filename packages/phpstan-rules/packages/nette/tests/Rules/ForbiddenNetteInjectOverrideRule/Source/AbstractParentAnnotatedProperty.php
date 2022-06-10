<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source;

/**
 * @property-read \stdClass $payload
 * @property-read \Nette\Security\User $user
 */
abstract class AbstractParentAnnotatedProperty
{
    private $user;
}
