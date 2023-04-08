<?php

namespace App\Controller\Back;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\ListArticles;
use App\Entity\ParagraphArticles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Service\ImageOptimizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    #[Route('/category/{name}', name: 'app_back_articles_list', methods: ['GET'])]
    public function categoryPage(ArticlesRepository $articlesRepository, Category $category): Response
    {
        $articles = $articlesRepository->findBy(['category' => $category]);
    
        return $this->render('back/articles/index.html.twig', [
            'articles' => $articles,
            'category' => $category,
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
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            

            // SLUG
            $slug = $this->slugger->slug($article->getTitle());
            $article->setSlug($slug);


            // IMAGE Principal
            $brochureFile = $form->get('imgPost')->getData();
            if (empty($brochureFile)) {
                $article->setImgPost('Accueil');
                $article->setAltImg('Une Taupe Chez Vous ! image de présentation');
            } else {
                $article->setImgPost($slug);
                $this->imageOptimizer->setPicture($brochureFile, $slug );
                
                // ALT IMG
                if (empty($article->getAltImg())) {
                    $article->setAltImg($article->getTitle());
                } else {
                    $article->setAltImg($article->getAltImg());
                }
            }
            
            // DATE
            $article->setCreatedAt(new DateTime());




            // IMAGE PARAGRAPH
            $brochureFileParagraph = $form->get('paragraphArticles')->getData();

            $paragraphArticles = $form->get('paragraphArticles')->getData();
            foreach ($paragraphArticles as $paragraph) {
                if (!empty($paragraph)) {
                    $brochureFileParagraph = $paragraph->getImgPostParagh();
                    $slugPara = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                    $slugPara = substr($slugPara, 0, 30); // 30 max
                    $paragraph->setImgPostParagh($slugPara);// set slug to image paragraph
                    $this->imageOptimizer->setPicture($brochureFileParagraph, $slugPara ); // set image paragraph
                }            
            } 
                // if (empty($brochureFileParagraph)) {
                //     foreach ($brochureFileParagraph as $paragraph) {
                //         $img = $paragraph->getImgPostParagh(); // get image paragraph
                //         $slugPara = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                //         $slugPara = substr($slugPara, 0, 30); // 30 max
                //         $paragraph->setImgPostParagh($slugPara); // set slug to image paragraph
                //         $this->imageOptimizer->setPicture($img, $slugPara ); // set image paragraph
                //     }            
                // } 
            
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
        $imgPost = $article->getImgPost();
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // SLUG
            $slug = $this->slugger->slug($article->getTitle());
            $article->setSlug($slug);
            
            
    
            // IMAGE Principal
            $brochureFile = $form->get('imgPost')->getData();
            if ($brochureFile !== null) {
                
                $article->setImgPost($slug);
                $this->imageOptimizer->setPicture($brochureFile, $article->getImgPost() );
                // ALT IMG
                if (empty($article->getAltImg())) {
                    $article->setAltImg($article->getTitle());
                } else {
                    $article->setAltImg($article->getAltImg());
                }
            } else {
                $article->setImgPost($slug);
            }
    
            // dd($article);
            
            
            // $brochureFile = $form->get('imgPost')->getData();
            // if (!empty($brochureFile)) {

            //     $slug = $this->slugger->slug($article->getTitle());
            //     $article->setImgPost($slug);
            //     $this->imageOptimizer->setPicture($brochureFile, $slug);
            //     $article->setAltImg($article->getAltImg() ?: $article->getTitle());
            //     dd($article);     
            // }
           
           // IMAGE PARAGRAPH

           $paragraphArticles = $form->get('paragraphArticles')->getData();
           foreach ($paragraphArticles as $paragraph) {
                $brochureFileParagraph = $paragraph->getImgPostParagh();
                    if ($brochureFileParagraph !== null) {
                        $slugPara = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                        $slugPara = substr($slugPara, 0, 30); // 30 max
                        $paragraph->setImgPostParagh($slugPara);// set slug to image paragraph
                        $this->imageOptimizer->setPicture($brochureFileParagraph, $slugPara ); // set image paragraph
                    }            
            } 

            $articlesRepository->save($article, true);

            $response = new RedirectResponse($this->generateUrl('app_back_articles_index'), Response::HTTP_SEE_OTHER);
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            
            return $response;
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
