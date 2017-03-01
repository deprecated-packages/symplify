# Symfony Security Voters

[![Build Status](https://img.shields.io/travis/Symplify/SymfonySecurityVoters.svg?style=flat-square)](https://travis-ci.org/Symplify/SymfonySecurityVoters)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymfonySecurityVoters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymfonySecurityVoters)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symfony-security-voters.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-security-voters)


## Install

```sh
composer require symplify/symfony-security-voters
```

### Nette

Register the extension:

```yaml
# app/config/config.neon
extensions:
    - Symplify\SymfonySecurityVoters\Adapter\Nette\DI\SymfonySecurityExtension
	- Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```


## Usage

### Your First Voter

First, [read Symfony cookbook](http://symfony.com/doc/current/cookbook/security/voters_data_permission.html)

Then **create new voter** that implements `Symfony\Component\Security\Core\Authorization\Voter\VoterInterface`
and register it as service:

```yaml
# app/config/config.neon

services:
	- App\SomeModule\Security\Voter\MyVoter
```

Then in place, where we need to validate access, we'll just use `NetteAuthorizationChecker`:


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
