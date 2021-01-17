<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\ConverterWorker;

use Nette\Neon\Entity;
use Nette\Utils\Strings;

final class EntityConverterWorker
{
    /**
     * @var string
     */
    private const ARGUMENTS = 'arguments';

    /**
     * @param mixed[] $servicesData
     * @return mixed[]
     */
    public function convertServiceNeonEntities(array $servicesData): array
    {
        foreach ($servicesData as $name => $service) {
            if (! $service instanceof Entity) {
                continue;
            }

            $servicesData = $this->convertServiceEntity($servicesData, $name, $service);
        }

        return $servicesData;
    }

    /**
     * @param int|string $name
     * @return mixed[]
     */
    private function convertServiceEntity(array $servicesData, $name, Entity $entity): array
    {
        $class = $entity->value;

        $serviceData = [
            'class' => $class,
            self::ARGUMENTS => $entity->attributes,
        ];

        // class-named service
        if (is_int($name)) {
            // is namespaced class?
            if (Strings::contains($serviceData['class'], '\\')) {
                unset($serviceData['class']);
            }

            // remove old name
            unset($servicesData[$name]);

            $name = $class;
        }

        $servicesData[$name] = $serviceData;

        return $servicesData;
    }
}
