<?php

namespace App\Service;

use Michelf\MarkdownExtra;
// use voku\helper\HtmlMin;

class MarkdownProcessor
{
    private $markdown;
    // private $minifier;

    public function __construct()
    {
        $this->markdown = new MarkdownExtra();
        // $this->minifier = new HtmlMin();
    }

    public function processMarkdown($markdownText)
    {
        $containsTable = preg_match('/\|.*\|/', $markdownText);
        $containsMarkdownElements = preg_match('/(\*\*|###)/', $markdownText);
        $containsNumberedList = preg_match('/^\d+\./m', $markdownText);
        $containsBulletedList = preg_match('/^\*/m', $markdownText);

        if ($containsTable === 1 || $containsMarkdownElements === 1 || $containsNumberedList === 1 || $containsBulletedList === 1) {
            return $this->markdown->transform($markdownText);
        }
        
        // $minifiedHtml = $this->minifier->minify();

        return $markdownText;
    }
}
