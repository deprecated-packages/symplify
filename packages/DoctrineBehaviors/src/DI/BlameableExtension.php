<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\ORM\Blameable\BlameableSubscriber;
use Nette\Utils\Validators;
use Zenify\DoctrineBehaviors\Blameable\UserCallable;

final class BlameableExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'isRecursive' => true,
        'trait' => Blameable::class,
        'userCallable' => UserCallable::class,
        'userEntity' => null
    ];

    public function loadConfiguration(): void
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);
        $builder = $this->getContainerBuilder();

        $userCallable = $this->buildDefinitionFromCallable($config['userCallable']);

        $builder->addDefinition($this->prefix('listener'))
            ->setClass(BlameableSubscriber::class, [
                '@' . $this->getClassAnalyzer()->getClass(),
                $config['isRecursive'],
                $config['trait'],
                '@' . $userCallable->getClass(),
                $config['userEntity']
            ])
            ->setAutowired(false)
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }

    /**
     * @param mixed[] $config
     */
    private function validateConfigTypes(array $config): void
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'trait', 'type');
        Validators::assertField($config, 'userCallable', 'string');
        Validators::assertField($config, 'userEntity', 'null|string');
    }
}
