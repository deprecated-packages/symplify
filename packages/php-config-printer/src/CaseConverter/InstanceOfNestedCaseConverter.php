<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\NestedCaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\Service\ServiceOptionNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\MethodName;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

/**
 * Handles this part:
 *
 * services:
 *     _instanceof: <---
 */
final class InstanceOfNestedCaseConverter implements NestedCaseConverterInterface
{
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    /**
     * @var ServiceOptionNodeFactory
     */
    private $serviceOptionNodeFactory;

    public function __construct(
        CommonNodeFactory $commonNodeFactory,
        ServiceOptionNodeFactory $serviceOptionNodeFactory
    ) {
        $this->commonNodeFactory = $commonNodeFactory;
        $this->serviceOptionNodeFactory = $serviceOptionNodeFactory;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        $classConstFetch = $this->commonNodeFactory->createClassReference($key);

        $servicesVariable = new Variable(VariableName::SERVICES);
        $args = [new Arg($classConstFetch)];

        $instanceofMethodCall = new MethodCall($servicesVariable, MethodName::INSTANCEOF, $args);
        $instanceofMethodCall = $this->serviceOptionNodeFactory->convertServiceOptionsToNodes(
            $values,
            $instanceofMethodCall
        );

        return new Expression($instanceofMethodCall);
    }

    public function match(string $rootKey, $subKey): bool
    {
        if ($rootKey !== YamlKey::SERVICES) {
            return false;
        }

        if (! is_string($subKey)) {
            return false;
        }

        return $subKey === YamlKey::_INSTANCEOF;
    }
}
