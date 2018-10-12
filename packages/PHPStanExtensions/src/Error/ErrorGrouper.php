<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Error;

use PHPStan\Analyser\Error;
use function Safe\usort;

final class ErrorGrouper
{
    /**
     * @param Error[] $errors
     * @return mixed[]
     */
    public function groupErrorsToMessagesToFrequency(array $errors): array
    {
        $errorMessagesWithFiles = [];

        foreach ($errors as $error) {
            $errorMessagesWithFiles[$error->getMessage()]['message'] = $error->getMessage();
            $errorMessagesWithFiles[$error->getMessage()]['files'][] = $error->getFile() . ':' . $error->getLine();
        }

        // sort with most frequent first
        usort(
            $errorMessagesWithFiles,
            function (array $firstErrorMessageWithFiles, array $secondErrorMessageWithFiles): int {
                return count($secondErrorMessageWithFiles['files']) <=> count($firstErrorMessageWithFiles['files']);
            }
        );

        return $errorMessagesWithFiles;
    }
}
