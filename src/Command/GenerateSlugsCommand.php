<?php

use App\Entity\ParagraphPosts;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSlugsCommand extends Command
{
    private $entityManager;
    private $slugify;

    public function __construct(EntityManagerInterface $entityManager, SlugifyInterface $slugify)
    {
        $this->entityManager = $entityManager;
        $this->slugify = $slugify;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:generate-slugs')
            ->setDescription('Generate slugs for existing articles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $articles = $this->entityManager->getRepository(ParagraphPosts::class)->findAll();

        foreach ($articles as $article) {
            $slug = $this->slugify->slugify($article->getTitle());
            $article->setSlug($slug);
            
            if (strlen($slug) > 30) {
                $slug = substr($slug, 0, 30);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Slugs generated successfully.');

        return 0;
    }
}
