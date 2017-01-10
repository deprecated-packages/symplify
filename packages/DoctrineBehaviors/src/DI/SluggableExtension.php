<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\ORM\Sluggable\SluggableSubscriber;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

final class SluggableExtension extends AbstractBehaviorExtension
{
    /**
     * @var array
     */
    private $defaults = [
        'isRecursive' => true,
        'trait' => Sluggable::class
    ];

    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('listener'))
            ->setClass(SluggableSubscriber::class, [
                '@' . $this->getClassAnalyzer()->getClass(),
                $config['isRecursive'],
                $config['trait']
            ])
            ->setAutowired(false)
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }

    /**
     * @throws AssertionException
     */
    private function validateConfigTypes(array $config)
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'trait', 'type');
    }
}
