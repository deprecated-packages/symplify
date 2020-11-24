<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory\Service;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class ServicesPhpNodeFactory
{
    /**
     * @var string
     */
    private const EXCLUDE = 'exclude';

    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;

    /**
     * @var AutoBindNodeFactory
     */
    private $autoBindNodeFactory;

    public function __construct(
        CommonNodeFactory $commonNodeFactory,
        ArgsNodeFactory $argsNodeFactory,
        AutoBindNodeFactory $autoBindNodeFactory
    ) {
        $this->commonNodeFactory = $commonNodeFactory;
        $this->argsNodeFactory = $argsNodeFactory;
        $this->autoBindNodeFactory = $autoBindNodeFactory;
    }

    public function createResource(string $serviceKey, array $serviceValues): Expression
    {
        $servicesLoadMethodCall = $this->createServicesLoadMethodCall($serviceKey, $serviceValues);

        $servicesLoadMethodCall = $this->autoBindNodeFactory->createAutoBindCalls(
            $serviceValues,
            $servicesLoadMethodCall,
            AutoBindNodeFactory::TYPE_SERVICE
        );

        if (! isset($serviceValues[self::EXCLUDE])) {
            return new Expression($servicesLoadMethodCall);
        }

        $exclude = $serviceValues[self::EXCLUDE];
        if (! is_array($exclude)) {
            $exclude = [$exclude];
        }

        $excludeValue = [];
        foreach ($exclude as $key => $singleExclude) {
            $excludeValue[$key] = $this->commonNodeFactory->createAbsoluteDirExpr($singleExclude);
        }

        $args = $this->argsNodeFactory->createFromValues([$excludeValue]);
        $excludeMethodCall = new MethodCall($servicesLoadMethodCall, self::EXCLUDE, $args);

        return new Expression($excludeMethodCall);
    }

    private function createServicesLoadMethodCall(string $serviceKey, $serviceValues): MethodCall
    {
        $servicesVariable = new Variable(VariableName::SERVICES);

        $resource = $serviceValues['resource'];

        $args = [];
        $args[] = new Arg(new String_($serviceKey));
        $args[] = new Arg($this->commonNodeFactory->createAbsoluteDirExpr($resource));

        return new MethodCall($servicesVariable, 'load', $args);
    }
}
