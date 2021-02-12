<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory\Service;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\ServiceOptionAnalyzer\ServiceOptionAnalyzer;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class ServiceOptionNodeFactory
{
    /**
     * @var ServiceOptionsKeyYamlToPhpFactoryInterface[]
     */
    private $serviceOptionKeyYamlToPhpFactories = [];

    /**
     * @var ServiceOptionAnalyzer
     */
    private $serviceOptionAnalyzer;

    /**
     * @param ServiceOptionsKeyYamlToPhpFactoryInterface[] $serviceOptionKeyYamlToPhpFactories
     */
    public function __construct(
        ServiceOptionAnalyzer $serviceOptionAnalyzer,
        array $serviceOptionKeyYamlToPhpFactories
    ) {
        $this->serviceOptionKeyYamlToPhpFactories = $serviceOptionKeyYamlToPhpFactories;
        $this->serviceOptionAnalyzer = $serviceOptionAnalyzer;
    }

    /**
     * @param mixed[] $servicesValues
     */
    public function convertServiceOptionsToNodes(array $servicesValues, MethodCall $methodCall): MethodCall
    {
        $servicesValues = $this->unNestArguments($servicesValues);

        foreach ($servicesValues as $key => $value) {
            if ($this->shouldSkip($key)) {
                continue;
            }

            foreach ($this->serviceOptionKeyYamlToPhpFactories as $serviceOptionKeyYamlToPhpFactory) {
                if (! $serviceOptionKeyYamlToPhpFactory->isMatch($key, $value)) {
                    continue;
                }

                $methodCall = $serviceOptionKeyYamlToPhpFactory->decorateServiceMethodCall(
                    $key,
                    $value,
                    $servicesValues,
                    $methodCall
                );

                continue 2;
            }
        }

        return $methodCall;
    }

    /**
     * @return array<string, mixed>
     */
    private function unNestArguments(array $servicesValues): array
    {
        if (! $this->serviceOptionAnalyzer->hasNamedArguments($servicesValues)) {
            return $servicesValues;
        }

        return [
            YamlServiceKey::ARGUMENTS => $servicesValues,
        ];
    }

    private function shouldSkip(string $key): bool
    {
        // options started by decoration_<option> are used as options of the method decorate().
        if (Strings::startsWith($key, 'decoration_')) {
            return true;
        }

        return $key === 'alias';
    }
}
