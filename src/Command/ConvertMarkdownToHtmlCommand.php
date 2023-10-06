<?php

namespace App\Command;

use App\Entity\Posts;
use App\Entity\ParagraphPosts;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use Michelf\MarkdownExtra;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:convert-markdown',
    description: 'Add a short description for your command',
)]
class ConvertMarkdownToHtmlCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Posts::class);
        $paragrahRepository = $this->entityManager->getRepository(ParagraphPosts::class);
        $paragraphs = $paragrahRepository->findAll();
        $articles = $repository->findAll();

        $markdown = new MarkdownExtra();
    

        foreach ($paragraphs as $paragraph) {
            $markdownText = $paragraph->getParagraph();
            if ($markdownText === null) {
                continue;
            }
            $containsTable = preg_match('/\|.*\|/', $markdownText);

            // Convertir Markdown en HTML
            if ($containsTable === 1) {
                $htmlText = $markdown->transform($markdownText);
                $paragraph->setParagraph($htmlText);
            } 

            $htmlText = preg_replace_callback('/(\*\*|###|_)([\s\S]*?)(\*\*|###|_)/', function ($matches) {
                $delimiter = $matches[1];
                $content = $matches[2];
                
                if ($delimiter === '**') {
                    // Convertir `** Contenu **` en gras (<strong>)
                    return '<strong>' . $content . '</strong>';
                } elseif ($delimiter === '###') {
                    // Convertir `### Contenu ###` en titre (<h3>)
                    return '<h3>' . $content . '</h3>';
                } elseif ($delimiter === '_') {
                    // Convertir `_Texte en italique_` en italique (<em>)
                    return '<em>' . $content . '</em>';
                }
            }, $markdownText);
            $paragraph->setParagraph($htmlText);
        }

        
        foreach ($articles as $article) {

            $markdownText = $article->getContents();
            if ($markdownText === null) {
                continue;
            }
            $containsTable = preg_match('/\|.*\|/', $markdownText);

            // Convertir Markdown en HTML
            if ($containsTable === 1) {
                $htmlText = $markdown->transform($markdownText);
                $article->setContents($htmlText);
            } 

            $htmlText = preg_replace_callback('/(\*\*|###|_)([\s\S]*?)(\*\*|###|_)/', function ($matches) {
                $delimiter = $matches[1];
                $content = $matches[2];
                
                if ($delimiter === '**') {
                    // Convertir `** Contenu **` en gras (<strong>)
                    return '<strong>' . $content . '</strong>';
                } elseif ($delimiter === '###') {
                    // Convertir `### Contenu ###` en titre (<h3>)
                    return '<h3>' . $content . '</h3>';
                } elseif ($delimiter === '_') {
                    // Convertir `_Texte en italique_` en italique (<em>)
                    return '<em>' . $content . '</em>';
                }
            }, $markdownText);
            $article->setContents($htmlText);
        }

        $this->entityManager->flush();

        $output->writeln('Markdown to HTML conversion completed.');

        return Command::SUCCESS;
    }
}
