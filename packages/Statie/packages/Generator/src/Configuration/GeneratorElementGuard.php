<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GeneratorElementGuard
{
    /**
     * @var string[]
     */
    private static $requiredKeys = ['variable', 'variable_global', 'path', 'layout', 'route_prefix'];

    /**
     * @param string|int $key
     * @param string|mixed $data
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

        if (isset($data['object'])) {
            self::ensureObjectExists($key, $data['object']);
            self::ensureObjectIsParentOfAbstractFile($key, $data['object']);
        }

        if (isset($data['object_sorter'])) {
            self::ensureObjectExists($key, $data['object_sorter']);
            self::ensureObjectIsInstanceOf($key, 'object_sorter', $data['object_sorter'], ObjectSorterInterface::class);
        }
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

    /**
     * @param int|string $key
     */
    private static function ensureObjectIsInstanceOf(
        $key,
        string $optionName,
        string $object,
        string $expectedType
    ): void {
        if (is_a($object, $expectedType, true)) {
            return;
        }

        throw new InvalidGeneratorElementDefinitionException(sprintf(
            'Value in "%s" must extend "%s". "%s" type given In "parameters > generators > %s".',
            $optionName,
            $expectedType,
            $object,
            $key
        ));
    }
}
