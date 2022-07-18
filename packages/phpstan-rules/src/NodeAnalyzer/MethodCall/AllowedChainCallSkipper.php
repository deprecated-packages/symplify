<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\MethodCall;

use DateTimeInterface;
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
        Definition::class,
        VersionNumber::class,
        Version::class,
        RouteCollection::class,
        'Stringy\Stringy',
        // also trinary logic ↓
        PassedByReference::class,
        DateTimeInterface::class,
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
     * @param class-string[] $extraAllowedTypes
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
