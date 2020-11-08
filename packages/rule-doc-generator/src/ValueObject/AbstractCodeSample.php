<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

abstract class AbstractCodeSample implements CodeSampleInterface
{
    /**
     * @var string
     */
    private $goodCode;

    /**
     * @var string
     */
    private $badCode;

    public function __construct(string $goodCode, string $badCode)
    {
        $this->goodCode = $goodCode;
        $this->badCode = $badCode;

        if ($this->goodCode === $this->badCode) {
            throw new ShouldNotHappenException('Good and bad code cannot be identical');
        }
    }

    public function getGoodCode(): string
    {
        return $this->goodCode;
    }

    public function getBadCode(): string
    {
        return $this->badCode;
    }
}
