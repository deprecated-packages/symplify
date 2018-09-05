<?php declare(strict_types=1);

namespace Symplify\PHPStan\Error;

use PHPStan\Analyser\Error;

final class ErrorGrouper
{
    /**
     * @param Error[] $errors
     * @return mixed[]
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

        $errorMessagesWithCountsAndFiles = [];

        foreach ($errors as $error) {
            $errorMessagesWithCountsAndFiles[$error->getMessage()]['message'] = $error->getMessage();
            $errorMessagesWithCountsAndFiles[$error->getMessage()]['count'] = $errorMessagesWithCounts[$error->getMessage()];
            $errorMessagesWithCountsAndFiles[$error->getMessage()]['files'][] = $error->getFile() . ':' . $error->getLine();
        }

        return $errorMessagesWithCountsAndFiles;
    }
}
