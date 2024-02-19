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
use Symfony\Component\Messenger\MessageBusInterface;
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
use \IntlDateFormatter;
use App\Service\MarkdownProcessor;
use App\Service\UrlGeneratorService;
use App\Message\UpdateNextAppMessage;
use Symfony\Component\String\UnicodeString;

#[Route('/posts')]
class PostsController extends AbstractController
{
    private $params;
    private $imageOptimizer;
    private $slugger;
    private $photoDir;
    private $projectDir;
    private $entityManager;
    private $markdown;
    private $markdownProcessor;
    private $messageBus;
    private $urlGeneratorService;

    public function __construct(
        ContainerBagInterface $params,
        ImageOptimizer $imageOptimizer,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        MarkdownProcessor $markdownProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorService $urlGeneratorService,
    )
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->imageOptimizer = $imageOptimizer;
        $this->slugger = $slugger;
        $this->projectDir =  $this->params->get('app.projectDir');
        $this->photoDir =  $this->params->get('app.imgDir');
        $this->markdown = new MarkdownExtra();
        $this->domainFront = $this->params->get('app.domain');
        $this->markdownProcessor = $markdownProcessor;
        $this->messageBus = $messageBus;
        $this->urlGeneratorService = $urlGeneratorService;
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
            if($post->getSlug() !== "Accueil") {
                $post->setSlug($slug);
                $categorySlug = $post->getCategory() ? $post->getCategory()->getSlug() : null;
                $subcategorySlug = $post->getSubcategory() ? $post->getSubcategory()->getSlug() : null;
            
                $url = $this->urlGeneratorService->generatePath($slug, $categorySlug, $subcategorySlug);
                $post->setUrl($url);
            } else {
                $post->setSlug('Accueil');
                $url = '';
                $post->setUrl($url);
            }


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
            $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'dd MMMM yyyy');
            $post->setCreatedAt(new DateTime());
            $createdAt = $formatter->format($post->getCreatedAt());

            $post->setFormattedDate('Publié le ' . $createdAt);
            
            // MARKDOWN TO HTML
            $contentsText = $post->getContents();
            
            $htmlText = $this->markdownProcessor->processMarkdown($contentsText);
            
            $cleanedText = strip_tags($htmlText);
            $cleanedText = new UnicodeString($cleanedText);
            $cleanedText = $cleanedText->ascii();

            $post->setContents($cleanedText);

            $post->setContentsHTML($htmlText);

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

            $this->triggerNextJsBuild();
            $postsRepository->save($post, true);

        }

        return $this->renderForm('back/posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    public function triggerNextJsBuild()
    {
        $client = HttpClient::create();
        $apiEndpoint = 'https://' . $this->domainFront . '/api/build-export-endpoint';

        $authToken = $this->params->get('app.authToken');
        $body = json_encode(['payload' => 'build']);
        $calculatedSignature = hash_hmac('sha256', $body, $authToken);
        $headers = [
            'Content-Type' => 'application/json',
            'x-hub-signature-256' => 'sha256=' . $calculatedSignature,
        ];

        
        try {
            // Effectuer la demande HTTP POST avec les en-têtes et le corps spécifiés
            $response = $client->request('POST', $apiEndpoint, [
                'headers' => $headers,
                'json' => ['action' => 'trigger_build'],
            ]);
    
            // Traiter la réponse de l'API Next.js
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                // La demande a réussi, retourner une réponse JSON avec le statut 'ok'
                return new JsonResponse(['status' => 'ok']);
            } else {
                // La demande a échoué avec un code d'état autre que 200, retourner une réponse avec le code d'état
                return new JsonResponse(['error' => 'Request failed with status code ' . $statusCode], $statusCode);
            }
        } catch (Throwable $e) {
            // Gérer les erreurs de transport telles que les erreurs de connexion, etc.
            return new JsonResponse(['error' => 'An error occurred while processing the request: ' . $e->getMessage()], 500);
        }
    }

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
                $categorySlug = $post->getCategory() ? $post->getCategory()->getSlug() : null;
                $subcategorySlug = $post->getSubcategory() ? $post->getSubcategory()->getSlug() : null;
            
                $url = $this->urlGeneratorService->generatePath($slug, $categorySlug, $subcategorySlug);
                $post->setUrl($url);
            } else {
                $post->setSlug('Accueil');
                $url = '/';
                $post->setUrl($url);
            }
            
            // IMAGE Principal
            $brochureFile = $form->get('imgPost')->getData();

            if (!empty($brochureFile)) {
                
                $post->setImgPost($slug);
                $this->imageOptimizer->setPicture($brochureFile, $post->getImgPost() );
                
            } else {
                $post->setImgPost($imgPost);
            }

            // MARKDOWN TO HTML
            $contentsText = $post->getContents();
            
            $htmlText = $this->markdownProcessor->processMarkdown($contentsText);
            
            $cleanedText = strip_tags($htmlText);

            $post->setContentsHTML($htmlText);

            // PARAGRAPH
            $paragraphPosts = $form->get('paragraphPosts')->getData();

            foreach ($paragraphPosts as $paragraph) {

                // MARKDOWN TO HTML
                $markdownText = $paragraph->getParagraph();

                $htmlText = $this->markdownProcessor->processMarkdown($markdownText);

                $paragraph->setParagraph($htmlText);

                // LINK
                $articleLink = $paragraph->getLinkPostSelect();
                if ($articleLink !== null) {
                    
                    $paragraph->setLinkSubtitle($articleLink->getTitle());
                    $slugLink = $articleLink->getSlug();

                    $categoryLink = $articleLink->getCategory()->getSlug();
                    if ($categoryLink === "Pages") {
                        $paragraph->setLink('/'.$slugLink);
                    }                     
                    if ($categoryLink === "Annuaire") {
                        $paragraph->setLink('/'.$categoryLink.'/'.$slugLink);
                    } 
                    if ($categoryLink === "Articles") {
                        $subcategoryLink = $articleLink->getSubcategory()->getSlug();
                        $paragraph->setLink('/'.$categoryLink.'/'.$subcategoryLink.'/'.$slugLink);
                    }
                } 
                
                // $deletedLink = $form['paragraphPosts'];

                // if ($deletedLink[$paragraphPosts->indexOf($paragraph)]['deleteLink']->getData() === true) {
                //     $paragraph->setLink(null);
                //     $paragraph->setLinkSubtitle(null);
                // }

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
                    
                    // ALT IMG PARAGRAPH
                    if (empty($paragraph->getAltImg())) {
                        $paragraph->setAltImg($paragraph->getSubtitle());
                    } 
                }
            } 

            // DATE
            $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'dd MMMM yyyy');
            $post->setUpdatedAt(new DateTime());
            $updatedDate = $formatter->format($post->getUpdatedAt());
            $createdAt = $formatter->format($post->getCreatedAt());

            $post->setFormattedDate('Publié le ' . $createdAt . '. Mise à jour le ' . $updatedDate);
            
            
            $postsRepository->save($post, true);
            // $this->triggerNextJsBuild();

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
                $paragraph->setLink(null);
                $paragraph->setLinkSubtitle(null);

                $this->entityManager->flush();

                $this->addFlash('success', 'Le lien a bien été supprimé.');
            
        }
        
        return $this->redirectToRoute('app_back_posts_edit', ['id' => $postId], Response::HTTP_SEE_OTHER);
    }
        

}
