<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\MethodCall;

<<<<<<< HEAD
use DateTimeInterface;
=======
>>>>>>> [PHPStanRules] Enable the no assign fluent
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PharIo\Version\Version;
use PharIo\Version\VersionNumber;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\TrinaryLogic;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\String\AbstractString;
use Symplify\PHPStanRules\Matcher\ObjectTypeMatcher;

final class AllowedChainCallSkipper
{
    /**
     * @var array<class-string|string>
     */
    private const ALLOWED_CHAIN_TYPES = [
        AbstractConfigurator::class,
        RouteConfigurator::class,
        Alias::class,
        Finder::class,
        // symfony
        AbstractString::class,
        // php-scoper finder
        'Isolated\Symfony\Component\Finder\Finder',
<<<<<<< HEAD
=======
        \React\ChildProcess\Process::class,
        \Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface::class,
>>>>>>> [PHPStanRules] Enable the no assign fluent
        Definition::class,
        VersionNumber::class,
        Version::class,
        RouteCollection::class,
<<<<<<< HEAD
        'Stringy\Stringy',
        // also trinary logic ↓
        PassedByReference::class,
        DateTimeInterface::class,
=======
        \Symfony\Component\Process\Process::class,
        \Symfony\Component\HttpFoundation\Request::class,
        \Symplify\MonorepoBuilder\Release\Process\ProcessRunner::class,
        \Symfony\Component\Console\Command\Command::class,
        \Latte\Engine::class,
        \Symfony\Component\HttpFoundation\RequestStack::class,
        'Stringy\Stringy',
        // also trinary logic ↓
        PassedByReference::class,
        \DOMElement::class,
        \DateTimeInterface::class,
        \Symplify\Astral\PhpDocParser\Contract\PhpDocNodeVisitorInterface::class,
        \Clue\React\NDJson\Encoder::class,
        \Nette\Loaders\RobotLoader::class,
>>>>>>> [PHPStanRules] Enable the no assign fluent
        // Doctrine
        QueryBuilder::class,
        Query::class,
        'Stringy\Stringy',
        // phpstan
        RuleErrorBuilder::class,
        TrinaryLogic::class,
    ];

    public function __construct(
        private ObjectTypeMatcher $objectTypeMatcher
    ) {
    }

    /**
<<<<<<< HEAD
     * @param class-string[] $extraAllowedTypes
=======
     * @param string[] $extraAllowedTypes
>>>>>>> [PHPStanRules] Enable the no assign fluent
     */
    public function isAllowedFluentMethodCall(Scope $scope, MethodCall $methodCall, array $extraAllowedTypes = []): bool
    {
        $allowedTypes = array_merge($extraAllowedTypes, self::ALLOWED_CHAIN_TYPES);

        if ($this->objectTypeMatcher->isExprTypes($methodCall, $scope, $allowedTypes)) {
            return true;
        }

        return $this->objectTypeMatcher->isExprTypes($methodCall->var, $scope, $allowedTypes);
    }
}
