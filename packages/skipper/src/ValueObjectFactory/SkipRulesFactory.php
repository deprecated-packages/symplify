<?php
declare(strict_types=1);

namespace Symplify\Skipper\ValueObjectFactory;

use Nette\Utils\Strings;
use Symplify\Skipper\ValueObject\SkipRules;

final class SkipRulesFactory
{
    /**
     * @var string[]
     */
    private $skippedClasses = [];

    /**
     * @var string[]
     */
    private $skippedCodes = [];

    /**
     * @var array<string, string[]|null>
     */
    private $skippedMessages = [];

    /**
     * @param mixed[] $skipped
     */
    public function createFromSkipParameter(array $skipped): SkipRules
    {
        $this->skippedClasses = [];
        $this->skippedCodes = [];
        $this->skippedMessages = [];

        foreach ($skipped as $key => $value) {
            if (is_int($key)) {
                $this->separateSkipItem($value, null);
                continue;
            }

            $this->separateSkipItem($key, $value);
        }

        return new SkipRules($this->skippedClasses, $this->skippedCodes, $this->skippedMessages);
    }

    private function separateSkipItem($key, $value): void
    {
        if (class_exists($key)) {
            $this->skippedClasses[$key] = $value;
            return;
        }

        if (class_exists((string) Strings::before($key, '.'))) {
            $this->skippedCodes[$key] = $value;
            return;
        }

        $this->skippedMessages[$key] = $value;
    }
}
