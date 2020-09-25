Hey

```php
<?php

use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

function () {
    $obj = new stdClass;
    $obj->test = array('test');
};
```
-----
Hey

```php
<?php

use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

function () {
    $obj = new stdClass;
    $obj->test = ['test'];
};
```
