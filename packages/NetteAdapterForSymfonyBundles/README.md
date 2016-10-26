# Register Symfony bundles as Nette extension

[![Build Status](https://img.shields.io/travis/Symplify/NetteAdapterForSymfonyBundles.svg?style=flat-square)](https://travis-ci.org/Symplify/NetteAdapterForSymfonyBundles)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/NetteAdapterForSymfonyBundles.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/NetteAdapterForSymfonyBundles)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/NetteAdapterForSymfonyBundles.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/NetteAdapterForSymfonyBundles)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/nette-adapter-for-symfony-bundles.svg?style=flat-square)](https://packagist.org/packages/Symplify/nette-adapter-for-symfony-bundles)
[![Latest stable](https://img.shields.io/packagist/v/Symplify/nette-adapter-for-symfony-bundles.svg?style=flat-square)](https://packagist.org/packages/Symplify/nette-adapter-for-symfony-bundles)

## Install

```sh
composer require symplify/nette-adapter-for-symfony-bundles
```

Register extension:

```yaml
# app/config/config.neon
extensions:
	symfonyBundles: Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundlesExtension
```


## Usage

Register Symfony bundles just like Nette extensions:

```yaml
symfonyBundles:
	bundles:
		# list all bundles like "your key": "bundle class"
		alice: Hautelook\AliceBundle\HautelookAliceBundle
	parameters:
		# and it's parameters (bound by same key name)
		alice:
			locale: cs_CZ
```

That's it!


## Features

### Tags

```yaml
extensions:
	symfonyBundles: Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundles

services:
	-
		class: Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle\NetteTagsSource\SomeCommandHandler
		tags:
			tactician.handler:
				- [command: Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle\NetteTagsSource\SomeCommand]

symfonyBundles:
	bundles:
		- League\Tactician\Bundle\TacticianBundle
```


### Service references

```yaml
extensions:
	symfonyBundles: Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundles

services:
	- Symplify\NetteAdapterForSymfonyBundles\Tests\Container\ParametersSource\CustomMiddleware

symfonyBundles:
	bundles:
		tactician: League\Tactician\Bundle\TacticianBundle

	parameters:
		tactician:
			commandbus:
				default:
					middleware:
						# this is reference to service registered in Nette
						- @Symplify\NetteAdapterForSymfonyBundles\Tests\Container\ParametersSource\CustomMiddleware
						- tactician.middleware.command_handler
```

## Testing

```bash
composer check-cs # see "scripts" section of composer.json for more details 
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

We would be happy to merge your feature then!
