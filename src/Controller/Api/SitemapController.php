<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Repository\PostsRepository;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/sitemap",name="api_sitemap_")
 */
class SitemapController extends ApiController
{
    /**
     * @Route("", name="full", methods={"GET"})
     */
    public function generateSitemap(PostsRepository $postsRepository): JsonResponse
    {

        $posts = $postsRepository->findAll();

        $priority = [
            'Accueil',
            'Contact',
            'Interventions',
            'Taupier-agree-professionnel-depuis-1994',
            'Foire-aux-questions',
        ];

        $filteredSlugs = [];
        foreach ($posts as $page) {
            $updatedAt = $page->getUpdatedAt();
            $updatedAtFormatted = $updatedAt ? $updatedAt->format('Y-m-d\TH:i:s') : null;
            
            $category = $page->getCategory()->getName();

            if ($page->getSlug() === "Accueil") {
                $filteredSlugs[] = [
                    'id' => $page->getId(),
                    'slug' => "",
                    'createdAt' => $page->getCreatedAt()->format('Y-m-d\TH:i:s'),
                    'updatedAt' => $updatedAtFormatted,
                    'priority' => '1.0',
                ];
            } elseif (in_array($page->getSlug(), $priority)) {
                $filteredSlugs[] = [
                    'id' => $page->getId(),
                    'slug' => $page->getSlug(),
                    'createdAt' => $page->getCreatedAt()->format('Y-m-d\TH:i:s'),
                    'updatedAt' => $updatedAtFormatted,
                    'priority' => '1.0',
                ];
            } elseif ($category === 'Pages') {
                $filteredSlugs[] = [
                    'id' => $page->getId(),
                    'slug' => $page->getSlug(),
                    'createdAt' => $page->getCreatedAt()->format('Y-m-d\TH:i:s'),
                    'updatedAt' => $updatedAtFormatted,
                    'priority' => '0.5',
                ];
            } elseif ($category === 'Articles') {
                $filteredSlugs[] = [
                    'id' => $page->getId(),
                    'slug' => 'Articles/' . $page->getSubcategory()->getName() . '/' . $page->getSlug(),
                    'createdAt' => $page->getCreatedAt()->format('Y-m-d\TH:i:s'),
                    'updatedAt' => $updatedAtFormatted,
                    'priority' => '0.5',
                ];
            } elseif ($category === $category) {
                $filteredSlugs[] = [
                    'id' => $page->getId(),
                    'slug' => $category . '/' . $page->getSlug(),
                    'createdAt' => $page->getCreatedAt()->format('Y-m-d\TH:i:s'),
                    'updatedAt' => $updatedAtFormatted,
                    'priority' => '0.5',
                ];
            } 

        }
    
        return new JsonResponse($filteredSlugs, Response::HTTP_OK);
    }
}