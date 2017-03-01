<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletable;
use Knp\DoctrineBehaviors\ORM\SoftDeletable\SoftDeletableSubscriber;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

final class SoftDeletableExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'isRecursive' => true,
        'trait' => SoftDeletable::class
    ];

    public function loadConfiguration(): void
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('listener'))
            ->setClass(SoftDeletableSubscriber::class, [
                '@' . $this->getClassAnalyzer()->getClass(),
                $config['isRecursive'],
                $config['trait']
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
        Validators::assertField($config, 'trait', 'type');
    }
}
