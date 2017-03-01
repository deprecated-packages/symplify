<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\DI\AbstractBehaviorExtensionSource;

use Nette\DI\ServiceDefinition;
use Symplify\DoctrineBehaviors\DI\AbstractBehaviorExtension;

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
