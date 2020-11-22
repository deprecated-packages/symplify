# PHP Config Printer

[![Downloads total](https://img.shields.io/packagist/dt/symplify/php-config-printer.svg?style=flat-square)](https://packagist.org/packages/symplify/php-config-printer/stats)

Print Symfony services array with configuration to to plain PHP file format thanks to this simple php-parser wrapper

## Install

```bash
composer require symplify/php-config-printer --dev
```

Register bundle in your Kernel:

```php
namespace App;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;

final class AppKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new PhpConfigPrinterBundle()];
    }
}
```

## Use

### 1. Only Configured Services

```php
use Symplify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;

/** @var SmartPhpConfigPrinter $smartConfigPrinter */
$config = [
    'SomeService' => [
        'key' => 'value',
    ],
];

$smartConfigPrinter->printConfiguredServices($config);
```

### 2. Full Config

```php
use Symplify\PhpConfigPrinter\YamlToPhpConverter;

class SomeClass
{
    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    public function __construct(YamlToPhpConverter $yamlToPhpConverter)
    {
        $this->yamlToPhpConverter = $yamlToPhpConverter;
    }

    public function run(): void
    {
        $phpFileContent = $this->yamlToPhpConverter->convertYamlArray([
            'parameters' => [
                'key' => 'value',
            ],
            'services' => [
                '_defaults' => [
                    'autowire' => true,
                    'autoconfigure' => true,
                ],
            ],
        ]);

        // dump the $phpFileContent file
        // ...
    }
}
```
