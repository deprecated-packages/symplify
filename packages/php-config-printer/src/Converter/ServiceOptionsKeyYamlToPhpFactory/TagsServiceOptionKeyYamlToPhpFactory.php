<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Converter\ServiceOptionsKeyYamlToPhpFactory;

use Nette\Utils\Arrays;
use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class TagsServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
     * @var string
     */
    private const TAG = 'tag';

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory
    ) {
    }

    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yamlLines,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        if ($this->isSingleLineYamlLines($yamlLines)) {
            /** @var string[] $yamlLines */
            $string = new String_($yamlLines[0]);
            return new MethodCall($methodCall, self::TAG, [new Arg($string)]);
        }

        foreach ($yamlLines as $yamlLine) {
            if (is_string($yamlLine)) {
                $arg = new Arg(BuilderHelpers::normalizeValue($yamlLine));
                $args = $this->argsNodeFactory->createFromValues($arg);

                $methodCall = new MethodCall($methodCall, self::TAG, $args);
                continue;
            }

            $args = [];
            $flattenedYmlLine = Arrays::flatten($yamlLine, true);
            foreach ($flattenedYmlLine as $singleNestedKey => $singleNestedValue) {
                if ($singleNestedKey === 'name') {
                    $args[] = new Arg(BuilderHelpers::normalizeValue($singleNestedValue));
                    unset($flattenedYmlLine[$singleNestedKey]);
                }
            }

            $restArgs = $this->argsNodeFactory->createFromValuesAndWrapInArray($flattenedYmlLine);
            $args = array_merge($args, $restArgs);

            $args = $this->removeEmptySecondArgArray($args);

            $methodCall = new MethodCall($methodCall, self::TAG, $args);
        }

        return $methodCall;
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return $key === YamlServiceKey::TAGS;
    }

    /**
     * @param mixed[] $yamlLines
     */
    private function isSingleLineYamlLines(array $yamlLines): bool
    {
        return count($yamlLines) === 1 && is_string($yamlLines[0]);
    }

    /**
     * @param Arg[] $args
     * @return Arg[]
     */
    private function removeEmptySecondArgArray(array $args): array
    {
        if (! isset($args[1])) {
            return $args;
        }

        $secondArgValue = $args[1]->value;
        if (! $secondArgValue instanceof Array_) {
            return $args;
        }

        if (count($secondArgValue->items) !== 0) {
            return $args;
        }

        unset($args[1]);

        return $args;
    }
}
