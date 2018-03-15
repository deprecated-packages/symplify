<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\IsIdentical;

final class FixedSameStringsConstraint extends IsIdentical
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct($value);

        $this->value = $value;
    }

    /**
     * @param mixed $other
     */
    protected function additionalFailureDescription($other): string
    {
        if ($other === $this->value
            || preg_replace('/(\r\n|\n\r|\r)/', "\n", $other) !== preg_replace('/(\r\n|\n\r|\r)/', "\n", $this->value)
        ) {
            return '';
        }

        return ' #Warning: Strings contain different line endings!'
            . 'Debug using remapping ["\r" => "R", "\n" => "N", "\t" => "T"]:'
            . "\n"
            . ' -' . str_replace(["\r", "\n", "\t"], ['R', 'N', 'T'], $other)
            . "\n"
            . ' +' . str_replace(["\r", "\n", "\t"], ['R', 'N', 'T'], $this->value);
    }
}
