<?php

class SkipCurlyNewline
{
    /**
     * @param array{
     *    firstName?: string,
     *    lastName?: string,
     *    address?: string,
     *    state?: string
     * } $fields
     */
    public function method(
        int $number,
        array $fields,
        string $someString
    ) {
        var_dump($number, $fields, $someString);
    }
}
?>
