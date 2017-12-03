<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;

final class GeneratorElementGuard
{
    /**
     * @var string[]
     */
    private static $requiredKeys = ['variable', 'path', 'layout', 'route_prefix', 'object'];

    /**
     * @param string|mixed[] $data
     */
    public static function ensureInputIsValid($data): void
    {
        if (! is_array($data)) {
            throw new InvalidGeneratorElementDefinitionException(sprintf(
                'Element in "parameters > generators > { }" must be array. "%s" given.',

                is_object($data) ? get_class($data) : $data
            ));
        }

        foreach (self::$requiredKeys as $requiredKey) {
            if (isset($data[$requiredKey])) {
                continue;
            }

            throw new InvalidGeneratorElementDefinitionException(sprintf(
                'Key "%s" is missing in "parameters > generators > { }".',
                $requiredKey
            ));
        }
    }
}
