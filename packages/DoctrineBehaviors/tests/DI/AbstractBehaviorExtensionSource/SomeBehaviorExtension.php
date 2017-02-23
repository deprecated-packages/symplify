<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\DI\AbstractBehaviorExtensionSource;

use Nette\DI\ServiceDefinition;
use Zenify\DoctrineBehaviors\DI\AbstractBehaviorExtension;

final class SomeBehaviorExtension extends AbstractBehaviorExtension
{
    public function getClassAnalyzerPublic(): ServiceDefinition
    {
        return parent::getClassAnalyzer();
    }

    public function buildDefinitionFromCallablePublic(?string $value): ?ServiceDefinition
    {
        return parent::buildDefinitionFromCallable($value);
    }
}
