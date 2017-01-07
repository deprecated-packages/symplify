<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;

final class ErrorMessageSorterTest extends TestCase
{
    public function testSortByFileAndLine()
    {
        $errorMessageSorter = new ErrorMessageSorter();

        $this->assertSame(
            $this->getExpectedSortedMessages(),
            $errorMessageSorter->sortByFileAndLine($this->getUnsortedMessages())
        );
    }

    private function getUnsortedMessages() : array
    {
        return [
            'filePath' => [
                [
                    'line' => 5
                ]
            ],
            'anotherFilePath' => [
                [
                    'line' => 15
                ], [
                    'line' => 5
                ]
            ]
        ];
    }

    private function getExpectedSortedMessages() : array
    {
        return [
            'anotherFilePath' => [
                [
                    'line' => 5
                ], [
                    'line' => 15
                ]
            ],
            'filePath' => [
                [
                    'line' => 5
                ]
            ]
        ];
    }
}
