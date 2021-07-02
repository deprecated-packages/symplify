<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\Exception\NotImplementedYetException;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;

final class SharedPublicServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private CommonNodeFactory $commonNodeFactory,
    ) {
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        if ($key === 'public') {
            if ($yaml === false) {
                return new MethodCall($methodCall, 'private');
            }

            return new MethodCall($methodCall, 'public');
        }

        if ($key === 'shared') {
            if ($yaml === false) {
                return new MethodCall($methodCall, 'share', [new Arg($this->commonNodeFactory->createFalse())]);
            }

            return new MethodCall($methodCall, 'share');
        }

        throw new NotImplementedYetException();
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, ['shared', 'public'], true);
    }
}
