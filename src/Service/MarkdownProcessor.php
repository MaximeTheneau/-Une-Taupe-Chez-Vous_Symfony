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
        // Supprimer les espaces inutiles après les balises ouvrantes
        $html = preg_replace('/\>\s+\</', '><', $html);

        // Supprimer les espaces inutiles avant les balises fermantes
        $html = preg_replace('/\s+\</', '<', $html);

        // Supprimer les espaces inutiles à la fin des lignes
        $html = preg_replace('/\s+$/m', '', $html);

        // Supprimer les commentaires HTML
        $html = preg_replace('/<!--(.*?)-->/s', '', $html);

        return $html;
    }
}
