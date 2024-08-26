<?php

namespace App\Service;

use Michelf\MarkdownExtra;

class MarkdownProcessor
{
    private $markdown;

    public function __construct()
    {
        $this->markdown = new MarkdownExtra();


    }

    public function processMarkdown($markdownText)
    {
        $unicodeHtml = json_encode($markdownText, JSON_UNESCAPED_UNICODE);

        $html = $this->markdown->transform($unicodeHtml);

        return $html;
    }

}
