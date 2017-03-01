# Modular Latte Filters

[![Build Status](https://img.shields.io/travis/Symplify/ModularLatteFilters.svg?style=flat-square)](https://travis-ci.org/Symplify/ModularLatteFilters)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ModularLatteFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ModularLatteFilters)
[![Downloads](https://img.shields.io/packagist/dt/symplify/modular-latte-filters.svg?style=flat-square)](https://packagist.org/packages/symplify/modular-latte-filters)


## Install

```sh
composer require symplify/modular-latte-filters
```

Register the extension:

```yaml
# app/config/config.neon
extensions:
	- Symplify\ModularLatteFilters\DI\ModularLatteFiltersExtension
```


## Usage

Create class implementing `Symplify\ModularLatteFilters\DI\FiltersProviderInterface`:

```php
namespace App\Modules\SomeModule\Latte;

use Symplify\ModularLatteFilters\DI\FiltersProviderInterface;


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



## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.