# Modular Latte Filters

[![Build Status](https://img.shields.io/travis/Zenify/ModularLatteFilters.svg?style=flat-square)](https://travis-ci.org/Zenify/ModularLatteFilters)
[![Quality Score](https://img.shields.io/scrutinizer/g/Zenify/ModularLatteFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Zenify/ModularLatteFilters)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Zenify/ModularLatteFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Zenify/ModularLatteFilters)
[![Downloads](https://img.shields.io/packagist/dt/zenify/modular-latte-filters.svg?style=flat-square)](https://packagist.org/packages/zenify/modular-latte-filters)
[![Latest stable](https://img.shields.io/packagist/v/zenify/modular-latte-filters.svg?style=flat-square)](https://packagist.org/packages/zenify/modular-latte-filters)


## Install

```sh
composer require zenify/modular-latte-filters
```

Register the extension:

```yaml
# app/config/config.neon
extensions:
	- Zenify\ModularLatteFilters\DI\ModularLatteFiltersExtension
```


## Usage

Create class implementing `Zenify\ModularLatteFilters\DI\FiltersProviderInterface`:

```php
namespace App\Modules\SomeModule\Latte;

use Zenify\ModularLatteFilters\DI\FiltersProviderInterface;


final class SomeFilters implements FiltersProviderInterface
{

	public function getFilters() : array
	{
		return [
			'double' => function ($value) {
				return $value * 2;
			}
		];
	}

}
```

Register it to `config.neon`:

```yaml
# app/config/config.neon
services:
	- App\Modules\SomeModule\Latte\SomeFilters
```

Use in any template:

```latte
{* app/templates/Homepage/default.latte *}

And your self-esteem is {$selfEsteem|double}
```

That's it!



## Testing

```sh
composer check-cs
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

We would be happy to merge your feature then!
