<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Exception\Configuration\InvalidGeneratorElementDefinitionException;
use Symplify\Statie\Renderable\File\AbstractFile;
use function Safe\sprintf;

final class GeneratorElementGuard
{
    /**
     * @var string[]
     */
    private $requiredKeys = ['variable', 'variable_global', 'path', 'layout', 'route_prefix'];

    /**
     * @param string|int $key
     * @param string|mixed $data
     */
    public function ensureInputIsValid($key, $data): void
    {
        $this->ensureIsArray($key, $data);
        $this->ensureRequiredKeysAreSet($key, $data);

        if (isset($data['object'])) {
            $this->ensureObjectExists($key, $data['object']);
            $this->ensureObjectIsInstanceOf($key, 'object', $data['object'], AbstractFile::class);
        }

        if (isset($data['object_sorter'])) {
            $this->ensureObjectExists($key, $data['object_sorter']);
            $this->ensureObjectIsInstanceOf(
                $key,
                'object_sorter',
                $data['object_sorter'],
                ObjectSorterInterface::class
            );
        }
    }

    /**
     * @param int|string $key
     */
    private function ensureObjectExists($key, string $object): void
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
    private function ensureObjectIsInstanceOf(
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

    /**
     * @param string|int $key
     * @param string|mixed $data
     */
    private function ensureIsArray($key, $data): void
    {
        if (is_array($data)) {
            return;
        }

        throw new InvalidGeneratorElementDefinitionException(sprintf(
            'Element in "parameters > generators > %s" must be array. "%s" given.',
            $key,
            is_object($data) ? get_class($data) : $data
        ));
    }

    /**
     * @param int|string $key
     * @param mixed[] $data
     */
    private function ensureRequiredKeysAreSet($key, array $data): void
    {
        foreach ($this->requiredKeys as $requiredKey) {
            if (isset($data[$requiredKey])) {
                continue;
            }

            throw new InvalidGeneratorElementDefinitionException(sprintf(
                'Key "%s" is missing. In "parameters > generators > %s".',
                $requiredKey,
                $key
            ));
        }
    }
}
