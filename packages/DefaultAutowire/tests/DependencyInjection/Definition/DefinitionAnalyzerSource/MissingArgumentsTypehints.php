<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

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
     * @param string $valueWithoutType
     * @param SomeService|null $someService
     * @param int $value
     */
    public function __construct($valueWithoutType, ?SomeService $someService, $value = 1)
    {
        $this->valueWithoutType = $valueWithoutType;
        $this->someService = $someService;
        $this->value = $value;
    }
}
