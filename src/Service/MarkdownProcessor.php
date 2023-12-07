<?php

namespace App\Service;

use Michelf\MarkdownExtra;
use MatthiasMullie\Minify;

class MarkdownProcessor
{
    private $markdown;
    private $minifier;

    public function __construct()
    {
        $this->markdown = new MarkdownExtra();
        $this->minifier = new Minify\HTML();
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
        
        $minifiedHtml = $this->minifyHtml($markdownText);
        return $minifiedHtml;
    }

    private function minifyHtml($html)
    {
        $this->minifier($html);

        return $html;
    }
}
