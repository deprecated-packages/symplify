# PHP Config Printer

[![Downloads total](https://img.shields.io/packagist/dt/symplify/php-config-printer.svg?style=flat-square)](https://packagist.org/packages/symplify/php-config-printer/stats)

Print Symfony services array with configuration to to plain PHP file format thanks to this simple php-parser wrapper

## Install

```bash
composer require symplify/php-config-printer --dev
```

Register config in your services:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PhpConfigPrinter\ValueObject\PhpConfigPrinterConfig;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(PhpConfigPrinterConfig::FILE_PATH);
};
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
use Symplify\ConfigTransformer\Converter\YamlToPhpConverter;

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
