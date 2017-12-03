<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GeneratorElementGuard
{
    /**
     * @var string[]
     */
    private static $requiredKeys = ['variable', 'path', 'layout', 'route_prefix', 'object'];

    /**
     * @param string|int $key
     * @param string|mixed[] $data
     */
    public static function ensureInputIsValid($key, $data): void
    {
        if (! is_array($data)) {
            throw new InvalidGeneratorElementDefinitionException(sprintf(
                'Element in "parameters > generators > %s" must be array. "%s" given.',
                $key,
                is_object($data) ? get_class($data) : $data
            ));
        }

        foreach (self::$requiredKeys as $requiredKey) {
            if (isset($data[$requiredKey])) {
                continue;
            }

            throw new InvalidGeneratorElementDefinitionException(sprintf(
                'Key "%s" is missing. In "parameters > generators > %s".',
                $requiredKey,
                $key
            ));
        }

        self::ensureObjectExists($key, $data['object']);
        self::ensureObjectIsParentOfAbstractFile($key, $data['object']);
    }

    /**
     * @param int|string $key
     */
    private static function ensureObjectExists($key, string $object): void
    {
        if (class_exists($object)) {
            return;
        }

        throw new InvalidGeneratorElementDefinitionException(sprintf(
            'Object class "%s" not found. In "parameters > generators > %s".',
            $object,
            $key
        ));
    }

    /**
     * @param int|string $key
     */
    private static function ensureObjectIsParentOfAbstractFile($key, string $object): void
    {
        if (is_a($object, AbstractFile::class, true)) {
            return;
        }

        throw new InvalidGeneratorElementDefinitionException(sprintf(
            'Object class "%s" must extend "%s". In "parameters > generators > %s".',
            $object,
            AbstractFile::class,
            $key
        ));
    }
}
