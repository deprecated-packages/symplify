<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Transformer;

final class CommentCleaner
{
    public function clearFromComment(string $content): string
    {
        $content = trim($content);
        $content = $this->trimCommentStart($content);
        $content = $this->trimContentBody($content);
        $content = $this->trimCommentEnd($content);

        return $content;
    }

    private function trimCommentStart(string $content): string
    {
        if (substr($content, 0, 2) === '//') {
            $content = substr($content, 2);
        }

        if (substr($content, 0, 1) === '#') {
            $content = substr($content, 1);
        }

        if (substr($content, 0, 3) === '/**') {
            $content = substr($content, 3);
        }

        if (substr($content, 0, 2) === '/*') {
            $content = substr($content, 2);
        }

        return $content;
    }

    private function trimCommentEnd(string $content): string
    {
        if (substr($content, -2) === '*/') {
            $content = substr($content, 0, -2);
        }

        return $content;
    }

    private function trimContentBody(string $content): string
    {
        if (isset($content[0]) && $content[0] === '*') {
            $content = substr($content, 1);
        }

        return $content;
    }
}
