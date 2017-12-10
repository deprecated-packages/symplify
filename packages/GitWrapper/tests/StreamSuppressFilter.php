<?php declare(strict_types=1);

namespace GitWrapper\Test;

/**
 * Intercepts data sent to STDOUT and STDERR and uses the echo construct to
 * output the data so we can capture it using normal output buffering.
 */
final class StreamSuppressFilter extends \php_user_filter
{
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            echo $bucket->data;
            $bucket->data = '';
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
