<?php

namespace App\Controller\Api;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @Route("/api/articles",name="api_articles_")
 */
class ArticlesController extends ApiController
{


    /**
     * @Route("/home", name="browse", methods={"GET"})
     */
    public function browse(ArticlesRepository $articlesRepository ): JsonResponse
    {
    
        $allArticles = $articlesRepository->findLastArticles();

        return $this->json(
            $allArticles,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_articles_browse"
                ]
            ]
        );
    }
        
    /**
     * @Route("/desc", name="desc", methods={"GET"})
     */
    public function desc(ArticlesRepository $articlesRepository ): JsonResponse
    {
    
        $allArticles = $articlesRepository->findDescArticles();

        return $this->json(
            $allArticles,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_articles_desc"
                ]
            ]
        );
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     */
    public function all(ArticlesRepository $articlesRepository ): JsonResponse
    {
    
        $allArticles = $articlesRepository->findAllArticles();

        return $this->json(
            $allArticles,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_articles_read"
                ]
            ]
        );
    }

    /**
     * @Route("/thumbnail/{slug}", name="thumbnail", methods={"GET"})
     */
    public function thumbnail(ArticlesRepository $articlesRepository, Articles $articles = null ): JsonResponse
    {
    
        if ($articles === null)
        {
            // on renvoie donc une 404
            return $this->json(
                [
                    "erreur" => "Page non trouvée",
                    "code_error" => 404
                ],
                Response::HTTP_NOT_FOUND,// 404
            );
        }
        #dd($allPages);

        return $this->json(
            $articles,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_articles_thumbnail"
                ]
            ]
        );
    }

    /**
     * @Route("/{slug}", name="read", methods={"GET"})
     */
    public function read(Articles $articles = null)
    {
        if ($articles === null)
        {
            // on renvoie donc une 404
            return $this->json(
                [
                    "erreur" => "Page non trouvée",
                    "code_error" => 404
                ],
                Response::HTTP_NOT_FOUND,// 404
            );
        }

        return $this->json(
            $articles,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_articles_read"
                ]
            ]);
    }

   

}
