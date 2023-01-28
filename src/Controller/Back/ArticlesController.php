<?php

namespace App\Controller\Back;

use App\Entity\Articles;
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
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $this->slugger->slug($article->getTitle());
            $article->setSlug($slug);

            // IMAGE 1
            $this->imageOptimizer->setPicture($form->get('imgPost')->getData(), $article, 'setImgPost', $slug );

            //IMAGE 2
            if ($form->get('imgPost2')->getData() != null) {
                $this->imageOptimizer->setPicture($form->get('imgPost2')->getData(), $article, 'setImgPost2', $slug );
            }

            // IMAGE 3
            if ($form->get('imgPost3')->getData() != null) {
            $this->imageOptimizer->setPicture($form->get('imgPost3')->getData(), $article, 'setImgPost3', $slug.'-3');
            }
            
            // IMAGE 4
            if ($form->get('imgPost4')->getData() != null) {
                $this->imageOptimizer->setPicture($form->get('imgPost4')->getData(), $article, 'setImgPost4', $slug.'-4');
            }
            $article->setCreatedAt(new DateTime());

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
}
