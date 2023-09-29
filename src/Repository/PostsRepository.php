<?php

namespace App\Repository;

use App\Entity\Posts;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    public function save(Posts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Posts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findAllPosts()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findLastPosts()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('CASE WHEN r.updatedAt IS NOT NULL THEN r.updatedAt ELSE r.createdAt END', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
    public function findByCategorySlug(string $slug, int $limit)
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    public function findKeywordByPosts($postId)
    {
                // Récupérez le premier ID de mot-clé associé à l'article de référence
                $qb = $this->createQueryBuilder('p')
                ->select('k.id')
                ->innerJoin('p.keywords', 'k')
                ->where('p.id = :postId')
                ->setParameter('postId', $postId)
                ->setMaxResults(1);
    
            $result = $qb->getQuery()->getResult();
    
            if (!empty($result)) {
                $keywordId = $result[0]['id'];
    
                // Récupérez les IDs des articles partageant le même mot-clé (y compris l'article de référence)
                $subQuery = $this->createQueryBuilder('p2')
                    ->select('p2.id')
                    ->innerJoin('p2.keywords', 'k2')
                    ->where('k2.id = :keywordId')
                    ->setParameter('keywordId', $keywordId)
                    ->getQuery()
                    ->getResult();
    
                // Supprimez l'article de référence de la liste
                $filteredIds = array_filter(
                    array_column($subQuery, 'id'),
                    function ($id) use ($postId) {
                        return $id != $postId;
                    }
                );
    
                // Maintenant, récupérez les articles en utilisant les IDs filtrés
                return $this->createQueryBuilder('p3')
                    ->where('p3.id IN (:filteredIds)')
                    ->setParameter('filteredIds', $filteredIds)
                    ->orderBy('CASE WHEN p3.updatedAt IS NOT NULL THEN p3.updatedAt ELSE p3.createdAt END', 'DESC')
                    ->setMaxResults(3)
                    ->getQuery()
                    ->getResult();
            }

        return []; // Aucun mot-clé trouvé, donc retourne un tableau vide

    }

//    /**
//     * @return Posts[] Returns an array of Posts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Posts
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
