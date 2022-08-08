<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveInterfacesRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();

    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);

    $rectorConfig->ruleWithConfiguration(RemoveInterfacesRector::class, [
        DocumentedRuleInterface::class,
        ConfigurableRuleInterface::class,
    ]);

    $rectorConfig->skip(['*/Tests/*', '*/tests/*', __DIR__ . '/../../tests']);
};
