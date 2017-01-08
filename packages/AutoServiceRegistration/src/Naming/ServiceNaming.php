<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Naming;

final class ServiceNaming
{
    public static function createServiceIdFromClass(string $class) : string
    {
        return strtr(strtolower($class), [
            '\\' => '.',
        ]);
    }
}
