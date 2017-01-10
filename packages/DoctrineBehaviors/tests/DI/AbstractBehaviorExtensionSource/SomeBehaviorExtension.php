<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\DI\AbstractBehaviorExtensionSource;

use Nette\DI\ServiceDefinition;
use Zenify\DoctrineBehaviors\DI\AbstractBehaviorExtension;

final class SomeBehaviorExtension extends AbstractBehaviorExtension
{
    public function getClassAnalyzerPublic() : ServiceDefinition
    {
        return parent::getClassAnalyzer();
    }

    /**
     * @param string|NULL
     * @return ServiceDefinition|NULL
     */
    public function buildDefinitionFromCallablePublic($value)
    {
        return parent::buildDefinitionFromCallable($value);
    }
}
