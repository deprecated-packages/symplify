<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\RoutingCaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\PhpConfigPrinter\Enum\RouteOption;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\Routing\ControllerSplitter;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class ImportRoutingCaseConverter implements RoutingCaseConverterInterface
{
    /**
     * @var string[]
     */
    private const NESTED_KEYS = [
        'name_prefix',
        RouteOption::DEFAULTS,
        'requirements',
        'options',
        'utf8',
        'condition',
        'host',
        'schemes',
        self::METHODS,
        RouteOption::CONTROLLER,
        'locale',
        'format',
        'stateless',
    ];

    /**
     * @var string[]
     */
    private const IMPORT_ARGS = [self::RESOURCE, self::TYPE, self::EXCLUDE];

    /**
     * @var string[]
     */
    private const PREFIX_ARGS = [
        // Add prefix itself as first argument
        self::PREFIX,
        'trailing_slash_on_root',
    ];

    /**
     * @var string
     */
    private const PREFIX = 'prefix';

    /**
     * @var string
     */
    private const RESOURCE = 'resource';

    /**
     * @var string
     */
    private const TYPE = 'type';

    /**
     * @var string
     */
    private const EXCLUDE = 'exclude';

    /**
     * @var string
     */
    private const METHODS = 'methods';

    private StringFormatConverter $stringFormatConverter;

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private ControllerSplitter $controllerSplitter
    ) {
        $this->stringFormatConverter = new StringFormatConverter();
    }

    /**
     * @param mixed[] $values
     */
    public function match(string $key, mixed $values): bool
    {
        return isset($values[self::RESOURCE]);
    }

    public function convertToMethodCall(string $key, mixed $values): Stmt
    {
        $variable = new Variable(VariableName::ROUTING_CONFIGURATOR);

        $args = $this->createAddArgs(self::IMPORT_ARGS, $values);
        $methodCall = new MethodCall($variable, 'import', $args);

        // Handle prefix independently as it has specific args
        if (isset($values[self::PREFIX])) {
            $args = $this->createAddArgs(self::PREFIX_ARGS, $values);
            $methodCall = new MethodCall($methodCall, self::PREFIX, $args);
        }

        foreach (self::NESTED_KEYS as $nestedKey) {
            if (! isset($values[$nestedKey])) {
                continue;
            }

            $nestedValues = $values[$nestedKey];

            if ($nestedKey === RouteOption::CONTROLLER) {
                $nestedValues = $this->controllerSplitter->splitControllerClassAndMethod($nestedValues);
            }

            // Transform methods as string GET|HEAD to array
            if ($nestedKey === self::METHODS && is_string($nestedValues)) {
                $nestedValues = explode('|', $nestedValues);
            }

            $args = $this->argsNodeFactory->createFromValues([$nestedValues]);
            $name = $this->stringFormatConverter->underscoreAndHyphenToCamelCase($nestedKey);

            $methodCall = new MethodCall($methodCall, $name, $args);
        }

        return new Expression($methodCall);
    }

    /**
     * @param string[] $argsNames
     * @return Arg[]
     */
    private function createAddArgs(array $argsNames, mixed $values): array
    {
        $argumentValues = [];

        foreach ($argsNames as $argName) {
            if (isset($values[$argName])) {
                // Default $ignoreErrors to false before $exclude on import(), same behaviour as symfony
                if ($argName === self::EXCLUDE) {
                    $argumentValues[] = false;
                }

                $argumentValues[] = $values[$argName];
            }
        }

        return $this->argsNodeFactory->createFromValues($argumentValues, true, false, false, true);
    }
}
