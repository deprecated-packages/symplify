# Doctrine Behaviors

[![Build Status](https://img.shields.io/travis/Zenify/DoctrineBehaviors.svg?style=flat-square)](https://travis-ci.org/Zenify/DoctrineBehaviors)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Zenify/DoctrineBehaviors.svg?style=flat-square)](https://scrutinizer-ci.com/g/Zenify/DoctrineBehaviors)
[![Downloads total](https://img.shields.io/packagist/dt/zenify/doctrine-behaviors.svg?style=flat-square)](https://packagist.org/packages/zenify/doctrine-behaviors)
[![Latest stable](https://img.shields.io/packagist/v/zenify/doctrine-behaviors.svg?style=flat-square)](https://packagist.org/packages/zenify/doctrine-behaviors)


Port of [KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors) to Nette DI

Supported behaviors:

- Blameable
- Geocodable
- Loggable
- Sluggable
- SoftDeletable
- Translatable
- Timestampable
- Tree

For implementation to entities, check [tests](https://github.com/KnpLabs/DoctrineBehaviors/tree/master/tests/fixtures/BehaviorFixtures/ORM).


## Install

Via Composer:

```sh
$ composer require zenify/doctrine-behaviors
```

Register extensions you need in `config.neon`:

```yaml
extensions:
	translatable: Zenify\DoctrineBehaviors\DI\TranslatableExtension
	- Zenify\DoctrineBehaviors\DI\TimestampableExtension
```


## Usage

### Translatable

Setup your translator locale callback in `config.neon`:

```yaml
translatable:
	currentLocaleCallable: [@Translator, getLocale]
```

Place trait to your entity:

```php
class Article
{
	
	use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
	// returns translated property for $article->getTitle() or $article->title
	use Zenify\DoctrineBehaviors\Entities\Attributes\Translatable;

}
```

And its translation entity:

```php
class ArticleTranslation
{
	
	use Knp\DoctrineBehaviors\Model\Translatable\Translation;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	public $title;

}
```

For deeper knowledge see test for:

- [TranslatableEntity](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/tests/fixtures/BehaviorFixtures/ORM/TranslatableEntity.php)
- [TranslatableEntityTranslation](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/tests/fixtures/BehaviorFixtures/ORM/TranslatableEntityTranslation.php)
- [theirs use](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/tests/Knp/DoctrineBehaviors/ORM/TranslatableTest.php)


### Timestampable

Place trait to your entity to ad `$createdAt` and `$updatedAt` properties:

```php
class Article
{
	
	use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

}
```



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
