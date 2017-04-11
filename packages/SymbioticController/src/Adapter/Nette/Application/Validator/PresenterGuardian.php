<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Application\Validator;

use Nette\Application\InvalidPresenterException;
use Nette\Utils\Strings;
use ReflectionClass;

final class PresenterGuardian
{
    /**
     * @var string
     */
    private const PRESENTER_NAME_PATTERN = '#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#';

    public function ensurePresenterNameIsValid(string $name): void
    {
        if (is_string($name)) {
            return;
        }

        if (Strings::match($name, self::PRESENTER_NAME_PATTERN)) {
            return;
        }

        throw new InvalidPresenterException(sprintf(
            'Presenter name must be alphanumeric string, "%s" is invalid.',
            $name
        ));
    }

    public function ensurePresenterClassExists(string $name, string $class): void
    {
        if (class_exists($class)) {
            return;
        }

        throw new InvalidPresenterException(sprintf(
            'Cannot load presenter "%s", class "%s" was not found.',
            $name,
            $class
        ));
    }

    public function ensurePresenterClassIsNotAbstract(string $name, string $class): void
    {
        $reflection = new ReflectionClass($class);

        if (! $reflection->isAbstract()) {
            return;
        }

        throw new InvalidPresenterException(sprintf(
            'Cannot load presenter "%s", class "%s" is abstract.',
            $name,
            $class
        ));
    }
}
