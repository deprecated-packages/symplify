<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\MethodCall;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ErrorType;
use Symplify\PHPStanRules\Matcher\ObjectTypeMatcher;

final class AllowedChainCallSkipper
{
    /**
     * @var array<class-string|string>
     */
    private const ALLOWED_CHAIN_TYPES = [
        'PhpParser\Builder',
        'DateTimeInterface',
        'Doctrine\ORM\Query',
        'Doctrine\ORM\QueryBuilder',
        'PharIo\Version\Version',
        'PharIo\Version\VersionNumber',
        'PHPStan\Reflection\PassedByReference',
        'PHPStan\Rules\RuleErrorBuilder',
        'PHPStan\TrinaryLogic',
        'Symfony\Component\DependencyInjection\Alias',
        'Symfony\Component\DependencyInjection\ContainerBuilder',
        'Symfony\Component\DependencyInjection\Definition',
        'Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator',
        'Symfony\Component\Routing\RouteCollection',
        'Symfony\Component\Routing\Loader\Configurator\RouteConfigurator',
        'Symfony\Component\Finder\Finder',
        'Symfony\Component\String\AbstractString',
        // symfony
        // php-scoper finder
        'Isolated\Symfony\Component\Finder\Finder',
        'React\ChildProcess\Process',
        'Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface',
        'Stringy\Stringy',
        // also trinary logic ↓
        'Symfony\Component\Process\Process',
        'Symfony\Component\HttpFoundation\Request',
        'Symplify\MonorepoBuilder\Release\Process\ProcessRunner',
        'Symfony\Component\Console\Command\Command',
        'Symfony\Component\Console\Application',
        'Symfony\Component\HttpFoundation\RequestStack',
        'PHPStan\PhpDocParser\Parser\TokenIterator',
        'DOMElement',
        'DateTimeInterface',
        'Symplify\Astral\PhpDocParser\Contract\PhpDocNodeVisitorInterface',
        'Clue\React\NDJson\Encoder',
        'React\Promise\Promise',
        'Nette\Loaders\RobotLoader',
        // mocks
        'PHPUnit\Framework\MockObject\Builder\InvocationMocker',
    ];

    public function __construct(
        private ObjectTypeMatcher $objectTypeMatcher
    ) {
    }

    /**
     * @param string[] $extraAllowedTypes
     */
    public function isAllowedFluentMethodCall(Scope $scope, MethodCall $methodCall, array $extraAllowedTypes = []): bool
    {
        $allowedTypes = array_merge($extraAllowedTypes, self::ALLOWED_CHAIN_TYPES);

        if ($this->objectTypeMatcher->isExprTypes($methodCall, $scope, $allowedTypes)) {
            return true;
        }

        // skip fluent call, possibly mock on final class
        $callerType = $scope->getType($methodCall->var);
        if ($callerType instanceof ErrorType) {
            return true;
        }

        return $this->objectTypeMatcher->isExprTypes($methodCall->var, $scope, $allowedTypes);
    }
}
