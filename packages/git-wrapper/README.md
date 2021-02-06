# PHP Wrapper around GIT

[![Total Downloads](https://img.shields.io/packagist/dt/symplify/git-wrapper.svg?style=flat-square)](https://packagist.org/packages/symplify/git-wrapper)

Git Wrapper provides a **readable API that abstracts challenges of executing Git commands from within a PHP process** for you.

- It's built upon the [`Symfony\Process`](https://symfony.com/doc/current/components/process.html) to execute the Git command with **cross-platform support** and uses the best-in-breed techniques available to PHP.
- This library also provides an SSH wrapper script and API method for developers to **easily specify a private key other than default** by using [the technique from StackOverflow](http://stackoverflow.com/a/3500308/870667).
- Finally, various commands are expected to be executed in the directory containing the working copy. **The library handles this transparently** so the developer doesn't have to think about it.

## Install

```bash
composer require symplify/git-wrapper
```

## Usage

```php
use Symplify\GitWrapper\GitWrapper;

// Initialize the library. If the path to the Git binary is not passed as
// the first argument when instantiating GitWrapper, it is auto-discovered.
require_once __DIR__ . '/vendor/autoload.php';

$gitWrapper = new GitWrapper();

// Optionally specify a private key other than one of the defaults
$gitWrapper->setPrivateKey(__DIR__ . '/path/to/private/key');

// Clone a repo into `/path/to/working/copy`, get a working copy object
$git = $gitWrapper->cloneRepository('git://github.com/symplify/git-wrapper.git', __DIR__ . '/path/to/working/copy');

// Create a file in the working copy
touch(__DIR__ . '/path/to/working/copy/text.txt');

// Add it, commit it, and push the change
$git->add(__DIR__ . '/test.txt');
$git->commit('Added the test.txt file as per the examples.');
$git->push();

// Render the output for operation
echo $git->push();

// Stream output of subsequent Git commands in real time to STDOUT and STDERR.
$gitWrapper->streamOutput();

// Execute an arbitrary git command.
// The following is synonymous with `git config -l`
$gitWrapper->git('config -l');
```

All command methods adhere to the following paradigm:

```php
$git->command($arg1, $arg2, ..., $options);
```

Replace `command` with the Git command being executed, e.g. `checkout`, `push`,
etc. The `$arg*` parameters are a variable number of arguments as they would be
passed to the Git command line tool. `$options` is an optional array of command
line options in the following format:

```php
$options = [
    'verbose' => true,
    // Passes the "--verbose" flag.
    't' => 'my-branch',
    // Passes the "-t my-branch" option.
];
```

#### Logging

Use the logger listener with [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compatible loggers such as [Monolog](https://github.com/Seldaek/monolog) to log commands that are executed.

```php
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symplify\GitWrapper\EventSubscriber\GitLoggerEventSubscriber;

// Log to a file named "git.log"
$logger = new Logger('git');
$logger->pushHandler(new StreamHandler('git.log', Logger::DEBUG));

// Instantiate the subscriber, add the logger to it, and register it.
$gitWrapper->addLoggerEventSubscriber(new GitLoggerEventSubscriber($logger));

$git = $gitWrapper->cloneRepository('git://github.com/symplify/git-wrapper.git', '/path/to/working/copy');

// The "git.log" file now has info about the command that was executed above.
```

## Gotchas

There are a few "gotchas" that are out of scope for this library to solve but might prevent a successful implementation of running Git via PHP.

### Missing HOME Environment Variable

Sometimes the `HOME` environment variable is not set in the Git process that is spawned by PHP. This will cause many Git operations to fail. It is advisable to set the `HOME` environment variable to a path outside of the document root that the web server has write access to. Note that this environment variable is only set for the process running Git and NOT the PHP process that is spawns it.

```php
$gitWrapper->setEnvVar('HOME', __DIR__ . '/path/to/private/writable/dir');
```

It is important that the storage is persistent as the `~/.gitconfig` file will be written to this location. See the following "gotcha" for why this is important.

### Missing Identity And Configurations

Many repositories require that a name and email address are specified. This data is set by running `git config [name] [value]` on the command line, and the configurations are usually stored in the `~/.gitconfig file`. When executing Git via PHP, however, the process might have a different home directory than the user who normally runs git via the command line. Therefore no identity is sent to the repository, and it will likely throw an error.

```php
// Set configuration options globally.
$gitWrapper->git('config --global user.name "User name"');
$gitWrapper->git('config --global user.email user@example.com');

// Set configuration options per repository.
$git->config('user.name', 'User name');
$git->config('user.email', 'user@example.com');
```

### Commits To Repositories With No Changes

Running `git commit` on a repository *with no changes* fails with exception. To prevent that, check changes like:

```php
if ($git->hasChanges()) {
    $git->commit('Committed the changes.');
}
```

### Permissions Of The GIT_SSH Wrapper Script

On checkout, the bin/git-ssh-wrapper.sh script should be executable. If it is not, git commands will fail if a non-default private key is specified.

```bash
$ chmod +x ./bin/git-ssh-wrapper.sh
```

### Timeout

There is a default timeout of 60 seconds. This might cause "issues" when you use the clone feature of bigger projects or with slow internet.

```php
$this->gitWrapper = new GitWrapper();
$this->gitWrapper->setTimeout(120);
```
