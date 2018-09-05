<?php declare(strict_types=1);

namespace Symplify\PHPStan\Error;

use Symplify\EasyCodingStandard\Error\Error;

final class ErrorGrouper
{
    /**
     * @param Error[] $errors
     * @return int[]
     */
    public function groupErrorsToMessagesToFrequency(array $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $errorMessagesWithCounts = array_count_values($errorMessages);

        // sort with most frequent items first
        arsort($errorMessagesWithCounts);

        return $errorMessagesWithCounts;
    }
}
