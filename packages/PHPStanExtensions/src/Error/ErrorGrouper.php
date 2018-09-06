<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Error;

use PHPStan\Analyser\Error;

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
        usort($errorMessagesWithFiles, function ($firstErrorMessageWithFiles, $secondErrorMessageWithFiles) {
            return count($firstErrorMessageWithFiles['files']) < count($secondErrorMessageWithFiles['files']);
        });

        return $errorMessagesWithFiles;
    }
}
