<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Geocodable\Geocodable;
use Knp\DoctrineBehaviors\ORM\Geocodable\GeocodableSubscriber;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

final class GeocodableExtension extends AbstractBehaviorExtension
{
    /**
     * @var array
     */
    private $defaults = [
        'isRecursive' => true,
        'trait' => Geocodable::class,
        'geolocationCallable' => null
    ];

    public function loadConfiguration() : void
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);
        $builder = $this->getContainerBuilder();

        $geolocationCallable = $this->buildDefinitionFromCallable($config['geolocationCallable']);
        $builder->addDefinition($this->prefix('listener'))
            ->setClass(GeocodableSubscriber::class, [
                '@' . $this->getClassAnalyzer()->getClass(),
                $config['isRecursive'],
                $config['trait'],
                $geolocationCallable ? '@' . $geolocationCallable->getClass() : $geolocationCallable
            ])
            ->setAutowired(false)
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }

    /**
     * @throws AssertionException
     */
    private function validateConfigTypes(array $config) : void
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'trait', 'type');
        Validators::assertField($config, 'geolocationCallable', 'callable');
    }
}
