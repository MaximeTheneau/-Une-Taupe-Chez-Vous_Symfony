<?php

namespace App\Controller\Back;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\ListArticles;
use App\Entity\ParagraphArticles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Service\ImageOptimizer;

#[Route('/articles')]
class ArticlesController extends AbstractController
{
    private $imageOptimizer;
    private $slugger;
    private $photoDir;
    private $params;
    private $projectDir;

    public function __construct(
        ContainerBagInterface $params,
        ImageOptimizer $imageOptimizer,
        SluggerInterface $slugger,
    )
    {
        $this->params = $params;
        $this->imageOptimizer = $imageOptimizer;
        $this->slugger = $slugger;
        $this->projectDir =  $this->params->get('app.projectDir');
        $this->photoDir =  $this->params->get('app.imgDir');
    }
    
    #[Route('/', name: 'app_back_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('back/articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_back_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticlesRepository $articlesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $article = new Articles();

        $category = new Category();
        
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        
        // Category
        $article->addCategory($category);
        
        if ($form->isSubmitted() && $form->isValid()) {
            

            // SLUG
            $slug = $this->slugger->slug($article->getTitle());
            $article->setSlug($slug);

            // IMAGE 1
            $this->imageOptimizer->setPicture($form->get('imgPost')->getData(), $article, 'setImgPost', $slug );

            // DATE
            $article->setCreatedAt(new DateTime());

            // IMAGE PARAGRAPH
            $paragraphArticles = $form->get('paragraphArticles')->getData();
            foreach ($paragraphArticles as $paragraph) {
                $img = $paragraph->getImgPostParagh(); // get image paragraph
                $slug = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                $slug = substr($slug, 0, 30); // 30 max
                $paragraph->setImgPostParagh($slug); // set slug to image paragraph
                $this->imageOptimizer->setPicture($img, $article, 'setImgPost', $slug ); // set image paragraph
            }

            $articlesRepository->save($article, true);

            return $this->redirectToRoute('app_back_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('back/articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($article->getTitle());
            $article->setSlug($slug);
            
            
            // IMAGE 1
            if ($form->get('imgPost')->getData() != null) {
                $this->imageOptimizer->setPicture($form->get('imgPost')->getData(), $article, 'setImgPost', $slug );
            }

            // IMAGE PARAGRAPH
            $paragraphArticles = $form->get('paragraphArticles')->getData();
            foreach ($paragraphArticles as $paragraph) {
                if ($paragraph->getImgPostParagh() != null) {
                $img = $paragraph->getImgPostParagh(); // get image paragraph
                $slug = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                $slug = substr($slug, 0, 30); // 30 max
                $paragraph->setImgPostParagh($slug); // set slug to image paragraph
                $this->imageOptimizer->setPicture($img, $article, 'setImgPost', $slug ); // set image paragraph
                }
            }

                
            $articlesRepository->save($article, true);
            
            return $this->redirectToRoute('app_back_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articlesRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_back_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/article/infinite-list", name="article_infinite_list")
     */
    public function infiniteList(Request $request): Response
    {
        
    }
}
