<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\ORM\Loggable\LoggableSubscriber;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use Symplify\DoctrineBehaviors\Loggable\LoggerCallable;

final class LoggableExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'isRecursive' => true,
        'loggerCallable' => LoggerCallable::class
    ];

    public function loadConfiguration(): void
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);
        $builder = $this->getContainerBuilder();

        $loggerCallable = $this->buildDefinitionFromCallable($config['loggerCallable']);

        $builder->addDefinition($this->prefix('listener'))
            ->setClass(LoggableSubscriber::class, [
                '@' . $this->getClassAnalyzer()->getClass(),
                $config['isRecursive'],
                '@' . $loggerCallable->getClass()
            ])
            ->setAutowired(false)
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }

    /**
     * @param mixed[] $config
     * @throws AssertionException
     */
    private function validateConfigTypes(array $config): void
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'loggerCallable', 'type');
    }
}
