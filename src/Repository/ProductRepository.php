<?php

namespace App\Repository;

use App\Entity\Product;
use App\Service\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Recherche qui permet de récupérer les produits en fonction de la recherche de l'utilisateur
     * @return Product[]
     */

    public function findWithSearch(Search $search)
    {
        // Crée une requête de recherche à partir de la table "p" (alias pour Product) et "c" (alias pour Category).
        $query = $this->createQueryBuilder('p') // Appelle de la méthode createQueryBuilder pour créer les requêtes
            ->select('c', 'p')
            ->join('p.category', 'c');

        // Si des catégories sont spécifiées dans la recherche, ajoute une condition à la requête.
        if (!empty($search->categories)){
            $query = $query
                ->andWhere('c.id IN (:category)')
                ->setParameter('category', $search->categories);
        }

        // Si une chaîne de caractères est spécifiée dans la recherche, ajoute une condition à la requête pour rechercher dans le nom du produit.
        if (!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }
        // Exécute la requête et renvoie les résultats.
        return $query->getQuery()->getResult();
    }



//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
