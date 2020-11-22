<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    public function __construct(CommonNodeFactory $commonNodeFactory)
    {
        $this->commonNodeFactory = $commonNodeFactory;
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        $method = $key;
        if ($key === 'shared') {
            $method = 'share';
        }

        $methodCall = new MethodCall($methodCall, $method);
        if ($yaml === false) {
            $methodCall->args[] = new Arg($this->commonNodeFactory->createFalse());
        }

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, [YamlServiceKey::BIND, YamlKey::AUTOWIRE, YamlKey::AUTOCONFIGURE], true);
    }
}
