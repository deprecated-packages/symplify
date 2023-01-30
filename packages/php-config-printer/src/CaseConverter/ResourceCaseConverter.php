<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Stmt;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\Service\ServicesPhpNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ResourceCaseConverter implements CaseConverterInterface
{
    public function __construct(
        private readonly ServicesPhpNodeFactory $servicesPhpNodeFactory
    ) {
    }

    public function convertToMethodCallStmt(mixed $key, mixed $values): Stmt
    {
        // Due to the yaml behavior that does not allow the declaration of several identical key names.
        if (isset($values['namespace'])) {
            $key = $values['namespace'];
            unset($values['namespace']);
        }

        return $this->servicesPhpNodeFactory->createResource($key, $values);
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        return isset($values[YamlKey::RESOURCE]);
    }
}
