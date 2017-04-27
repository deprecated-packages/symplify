<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class MissingArgumentsTypehints
{
    /**
     * @var string
     */
    private $valueWithoutType;

    /**
     * @var null|SomeService
     */
    private $someService;

    /**
     * @var int
     */
    private $value;

    /**
     * @param mixed $valueWithoutType
     */
    public function __construct($valueWithoutType, ?SomeService $someService, int $value = 1)
    {
        $this->valueWithoutType = $valueWithoutType;
        $this->someService = $someService;
        $this->value = $value;
    }

    public function getValueWithoutType(): string
    {
        return $this->valueWithoutType;
    }

    public function getSomeService(): SomeService
    {
        return $this->someService;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
