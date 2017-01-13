# Symplify/SymfonySecurity

[![Build Status](https://img.shields.io/travis/Symplify/SymfonySecurity.svg?style=flat-square)](https://travis-ci.org/Symplify/SymfonySecurity)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymfonySecurity.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymfonySecurity)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symfony-security.svg?style=flat-square)](htptps://packagist.org/packages/symplify/symfony-security)
[![Latest stable](https://img.shields.io/packagist/v/symplify/symfony-security.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-security)


## Install

```sh
composer require symplify/symfony-security
```

### Nette

Register the extension:

```yaml
# app/config/config.neon
extensions:
    - Symplify\SymfonySecurityVoters\Adapter\Nette\DI\SymfonySecurityExtension
	- Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```


### Symfony

@todo


## Usage

### Voters

First, [read Symfony cookbook](http://symfony.com/doc/current/cookbook/security/voters_data_permission.html)

Then create new voter implementing `Symfony\Component\Security\Core\Authorization\Voter\VoterInterface`
and register it as service in `config.neon`:

```yaml
services:
	- App\SomeModule\Security\Voter\MyVoter
```

Then in place, where we need to validate access, we'll just use `AuthorizationChecker`:


```php
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class Presenter
{

	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorizationChecker;

	
	public function __construct(AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}


	/**
	 * @param PresenterComponentReflection $element
	 */
	public function checkRequirements($element)
	{
		if ($this->authorizationChecker->isGranted('access', $element) === FALSE) {
			throw new ForbiddenRequestException;
		}
	}

}
```


That's it!


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
