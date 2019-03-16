<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Php;

use Nette\Utils\Strings;

final class TypeAnalyzer
{
    public function isPhpReservedType(string $type): bool
    {
        return in_array(
            $type,
            [
                'string',
                'bool',
                'mixed',
                'object',
                'iterable',
                'resource',
                'array',
                'float',
                'int',
                'boolean',
                'integer',
                'double',
                'null',
                'false',
                'true',
                'mixed',
            ],
            true
        );
    }

    public function isIterableType(string $type): bool
    {
        return Strings::endsWith($type, '[]');
    }
}
