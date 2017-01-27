# Symplify/SymfonySecurity

[![Build Status](https://img.shields.io/travis/Symplify/SymfonySecurity.svg?style=flat-square)](https://travis-ci.org/Symplify/SymfonySecurity)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymfonySecurity.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymfonySecurity)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symfony-security.svg?style=flat-square)](htptps://packagist.org/packages/symplify/symfony-security)
[![Latest stable](https://img.shields.io/packagist/v/symplify/symfony-security.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-security)


## Install

```sh
composer require symplify/symfony-security
```

Register the extension:

```yaml
# app/config/config.neon
extensions:
    - Symplify\SymfonySecurity\Adapter\Nette\DI\SymfonySecurityExtension
	- Symplify\SymfonyEventDispatcher\DI\SymfonyEventDispatcherExtension
```


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


### Firewalls

Original [Symfony firewalls](http://symfony.com/doc/current/components/security/firewall.html) pretty simplified and with modular support by default.

All we need to create is a **matcher** and a **listener**.

#### Request Matcher 

This service will match all sites in admin module - urls starting with `/admin`:

```php
use Symfony\Component\HttpFoundation\Request;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;


class AdminRequestMatcher implements RequestMatcherInterface
{

	public function getFirewallName() : string
	{
		return 'adminSecurity';
	}
	
	
	public function matches(Request $request) : bool
	{
		$url = $request->getPathInfo();
		return strpos($url, '/admin') === 0;
	}

}
```


### Firewall Listener

It will ensure that user is logged in and has 'admin' role, otherwise redirect.

```php
use Nette\Application\AbortException;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Security\User;
use Symplify\SymfonySecurity\Contract\Http\FirewallListenerInterface;


class LoggedAdminFirewallListener implements FirewallListenerInterface
{

	/**
	 * @var User
	 */
	private $user;
	

	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	
	public function getFirewallName() : string
	{
		return 'adminSecurity';
	}

	
	public function handle(Application $application, Request $applicationRequest)
	{
		if ( ! $this->user->isLoggedIn()) {
			throw new AbortException;
		}

		if ( ! $this->user->isInRole('admin')) {
			throw new AbortException;
		}
	}

}
```


Then we register both services.

```yaml
services:
	- AdminRequestMatcher
	- LoggedAdminFirewallListener
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
