<?php

namespace App\Controller\Back;

use App\Entity\Posts;
use App\Entity\Category;
use App\Entity\ListPosts;
use App\Entity\ParagraphPosts;
use App\Entity\Keyword;
use App\Form\PostsType;
use App\Form\ParagraphPostsType;
use App\Repository\PostsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ParagraphPostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Service\ImageOptimizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Michelf\MarkdownExtra;

#[Route('/posts')]
class PostsController extends AbstractController
{
    private $imageOptimizer;
    private $slugger;
    private $photoDir;
    private $params;
    private $projectDir;
    private $entityManager;
    private $markdown;


    public function __construct(
        ContainerBagInterface $params,
        ImageOptimizer $imageOptimizer,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->imageOptimizer = $imageOptimizer;
        $this->slugger = $slugger;
        $this->projectDir =  $this->params->get('app.projectDir');
        $this->photoDir =  $this->params->get('app.imgDir');
        $this->markdown = new MarkdownExtra();
    }
    
    #[Route('/', name: 'app_back_posts_index', methods: ['GET'])]
    public function index(PostsRepository $postsRepository): Response
    {
        return $this->render('back/posts/index.html.twig', [
            'posts' => $postsRepository->findAll(),
        ]);
    }

    #[Route('/category/{name}', name: 'app_back_posts_list', methods: ['GET'])]
    public function categoryPage(PostsRepository $postsRepository, Category $category): Response
    {
        $posts = $postsRepository->findBy(['category' => $category]);
    
        return $this->render('back/posts/index.html.twig', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    #[Route('/new', name: 'app_back_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostsRepository $postsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $post = new Posts();

        $category = new Category();
        
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {

            // SLUG
            $slug = $this->slugger->slug($post->getTitle());
            $post->setSlug($slug);


            // IMAGE Principal
            $brochureFile = $form->get('imgPost')->getData();
            if (empty($brochureFile)) {
                $post->setImgPost('Accueil');
                $post->setAltImg('Une Taupe Chez Vous ! image de présentation');
            } else {
                $post->setImgPost($slug);
                $this->imageOptimizer->setPicture($brochureFile, $slug );
                
            }

            // ALT IMG
            if (empty($post->getAltImg())) {
                $post->setAltImg($post->getTitle());
            } else {
                $post->setAltImg($post->getAltImg());
            }
            
            // DATE
            $post->setCreatedAt(new DateTime());

            // PARAGRAPH
            $paragraphPosts = $form->get('paragraphPosts')->getData();
            foreach ($paragraphPosts as $paragraph) {
                if (!empty($paragraph->getSubtitle())) {
                    // SLUG
                    $slugPara = $this->slugger->slug($paragraph->getSubtitle());
                    $slugPara = substr($slugPara, 0, 30); 
                    $paragraph->setSlug($slugPara);

                } else {
                    $this->entityManager->remove($paragraph);
                    $this->entityManager->flush();
                    }

                 // IMAGE PARAGRAPH

                 $imgPostParaghFile = $paragraph->getImgPostParaghFile();

                 if ($imgPostParaghFile !== null ) {
                     dd($imgPostParaghFile);
                     $brochureFileParagraph = $paragraph->getImgPostParagh();
                     // SLUG
                     $slugPara = $this->slugger->slug($paragraph->getSubtitle()); // slugify
                     $slugPara = substr($slugPara, 0, 30); // 30 max
                     $paragraph->setImgPostParagh($slugPara);// set slug to image paragraph
                     // Cloudinary
                     $this->imageOptimizer->setPicture($brochureFileParagraph, $slugPara ); // set image paragraph
                 } 
 
                 // ALT IMG PARAGRAPH
                 if (empty($paragraph->getAltImg())) {
                     $paragraph->setAltImg($paragraph->getSubtitle());
                 } else {
                     $paragraph->setAltImg($paragraph->getAltImg());
                 }          
            } 

            $postsRepository->save($post, true);

        }

        return $this->renderForm('back/posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    // public function triggerNextJsBuild()
    // {
    //     $client = HttpClient::create();
    //     $response = $client->request('POST', 'http://localhost:3000/api/build-export-endpoint', [
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //         ],
    //         'body' => json_encode([
    //             'trigger' => 'build',
    //         ]),
    //     ]);
    // }

    #[Route('/{id}', name: 'app_back_posts_show', methods: ['GET'])]
    public function show(Posts $post): Response
    {
        return $this->render('back/posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Posts $post, $id, ParagraphPostsRepository $paragraphPostsRepository, PostsRepository $postsRepository): Response
    {
        $imgPost = $post->getImgPost();
        
        $articles = $postsRepository->findAll();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        $paragraphPosts = $paragraphPostsRepository->find($id);
        
        
        $formParagraph = $this->createForm(ParagraphPostsType::class, $paragraphPosts);
        $formParagraph->handleRequest($request);

        $postExist = $postsRepository->find($id);

        if ($form->isSubmitted() && $form->isValid() ) {


            // SLUG
            $slug = $this->slugger->slug($post->getTitle());
            if($post->getSlug() !== "Accueil") {
                $post->setSlug($slug);
            } else {
                $post->setSlug('Accueil');
            }
            
            $image = $formParagraph->get('imgPostParagh')->getData();
    
            // IMAGE Principal
            $brochureFile = $form->get('imgPost')->getData();

            if (!empty($brochureFile)) {
                
                $post->setImgPost($slug);
                $this->imageOptimizer->setPicture($brochureFile, $post->getImgPost() );
                
            } else {
                $post->setImgPost($imgPost);
            }
            
            // PARAGRAPH
            $paragraphPosts = $form->get('paragraphPosts')->getData();
            foreach ($paragraphPosts as $paragraph) {
                $markdownText = $paragraph->getParagraph();

                $containsTable = preg_match('/\|.*\|/', $markdownText);
                $containsMarkdownElements = preg_match('/(\*\*|###)/', $markdownText);
                $containsNumberedList = preg_match('/^\d+\./m', $markdownText);
                $containsBulletedList = preg_match('/^\*/m', $markdownText);
    
                if ($containsTable === 1 || $containsMarkdownElements === 1 || $containsNumberedList === 1 || $containsBulletedList === 1) {
                    $htmlText = $this->markdown->transform($markdownText);
                    $paragraph->setParagraph($htmlText);
                } 
                // LINK

                $articleLink = $paragraph->getLinkPostSelect();
                if ($articleLink !== null) {
                    
                    
                    $paragraph->setLinkSubtitle($articleLink->getTitle());
                    $slugLink = $articleLink->getSlug();

                    $categoryLink = $articleLink->getCategory()->getSlug();
                    if ($articleLink->getSubcategory() === null || $categoryLink === "Pages") {
                        $paragraph->setLink('/'.$slugLink);
                    } else {
                        $subcategoryLink = $articleLink->getSubcategory()->getSlug();
                        $paragraph->setLink('/'.$categoryLink.'/'.$subcategoryLink.'/'.$slugLink);
                    }
                } 
                $deletedLink = $form['paragraphPosts'][$paragraphPosts->indexOf($paragraph)]['deleteLink']->getData();

                if ($deletedLink === true) {
                    $paragraph->setLink(null);
                    $paragraph->setLinkSubtitle(null);
                }


                // SLUG
                if (!empty($paragraph->getSubtitle())) {
                    $slugPara = $this->slugger->slug($paragraph->getSubtitle());
                    $slugPara = substr($slugPara, 0, 30); 
                    $paragraph->setSlug($slugPara);

                } else {
                    $this->entityManager->remove($paragraph);
                    $this->entityManager->flush();
                    }

                // IMAGE PARAGRAPH
                if (!empty($paragraph->getImgPostParaghFile())) {
                    $brochureFileParagraph = $paragraph->getImgPostParaghFile();
                    $slugPara = $this->slugger->slug($paragraph->getSubtitle());
                    $slugPara = substr($slugPara, 0, 30);
                    $paragraph->setImgPostParagh($slugPara);
                    $this->imageOptimizer->setPicture($brochureFileParagraph, $slugPara);
                }

                // ALT IMG PARAGRAPH
                if (empty($paragraph->getAltImg())) {
                    $paragraph->setAltImg($paragraph->getSubtitle());
                } else {
                    $paragraph->setAltImg($paragraph->getAltImg());
                }

            } 
            
            $post->setUpdatedAt(new DateTime());

            $postsRepository->save($post, true);

            return $this->redirectToRoute('app_back_posts_edit', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }
    

        return $this->renderForm('back/posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'articles' => $articles,
        ]);
    }

    #[Route('/{id}', name: 'app_back_posts_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $post, PostsRepository $postsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postsRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_back_posts_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deleted', name: 'app_back_posts_paragraph_deleted', methods: ['GET', 'POST'])]
    public function deleteParagraph(Request $request, $id, PostsRepository $postsRepository, ParagraphPosts $paragraphPosts, ParagraphPostsRepository $paragraphPostsRepository): Response
    {

        $paragraph = $paragraphPostsRepository->find($id);

        $post = $postsRepository->find($id);
        $postId = $paragraph->getPosts()->getId();
        if ($this->isCsrfTokenValid('delete' . $paragraph->getId(), $request->request->get('_token'))) {
            dd("ok");
                $paragraph->setLink(null);
                $paragraph->setLinkSubtitle(null);

                $this->entityManager->flush();

                $this->addFlash('success', 'Le lien a bien été supprimé.');
            
        }
        
        return $this->redirectToRoute('app_back_posts_edit', ['id' => $postId], Response::HTTP_SEE_OTHER);
    }
        

}
