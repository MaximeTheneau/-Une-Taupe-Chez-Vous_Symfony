<?php

namespace App\Controller\Api;

use App\Entity\Faq;
use App\Repository\FaqRepository;
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
 * @Route("/api/faq",name="api_faq_")
 */
class FaqController extends ApiController
{


    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(FaqRepository $faqRepository ): JsonResponse
    {
    
        $allfaq = $faqRepository->findAll();
        #dd($allfaq);

        return $this->json(
            $allfaq,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "api_faq_browse"
                ]
            ]
        );
    }
    

}
