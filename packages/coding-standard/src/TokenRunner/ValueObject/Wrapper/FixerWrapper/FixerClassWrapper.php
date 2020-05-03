<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class FixerClassWrapper
{
    /**
     * @var int
     */
    private $startBracketIndex;

    /**
     * @var mixed[]
     */
    private $propertyElements = [];

    /**
     * @var Tokens
     */
    private $tokens;

    public function __construct(Tokens $tokens, int $startIndex)
    {
        $this->startBracketIndex = $tokens->getNextTokenOfKind($startIndex, ['{']);
        $this->tokens = $tokens;
    }

    /**
     * @return mixed[]
     */
    public function getPropertyElements(): array
    {
        if ($this->propertyElements !== []) {
            return $this->propertyElements;
        }

        return $this->propertyElements = $this->getElementsByType('property');
    }

    /**
     * @return mixed[]
     */
    private function getElementsByType(string $type): array
    {
        $elements = (new PrivatesCaller())->callPrivateMethod(
            new OrderedClassElementsFixer(),
            'getElements',
            $this->tokens,
            $this->startBracketIndex
        );

        $methodElements = array_filter($elements, function (array $element) use ($type): bool {
            return $element['type'] === $type;
        });

        // re-index from 0
        return array_values($methodElements);
    }
}
