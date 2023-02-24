<?php

namespace App\Repository;

use App\Entity\Product;
use App\Services\StripeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Stripe\Exception\ApiErrorException;

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

    /**
     * @var StripeService
     */
    protected StripeService $stripeService;

    /**
     * @param ManagerRegistry $registry
     * @param StripeService $stripeService
     */
    public function __construct(
        ManagerRegistry $registry,
        StripeService $stripeService
    )
    {
        $this->stripeService = $stripeService;
        parent::__construct($registry, Product::class);
    }

    /**
     * @param Product $entity
     * @param bool $flush
     * @return void
     */
    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Get the given number of products from the database in descending order
    public function getLastProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRandomProducts(int $limit = 10): array
    {
        $products = [];
        $count = $this->count([]);

        for ($i = 0; $i < $limit; $i++) {
            $random = rand(1, $count);
            $product = $this->find($random);

            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @param Product $product
     * @return string|null
     * @throws ApiErrorException
     */
    public function getIntentSecret(array $products):string|null
    {
        $intent = $this->stripeService->paymentIntent($products);
        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameters
     * @param Product $product
     * @return array|null
     */
    public function stripePayment(array $stripeParameters, Product $product): ?array
    {
        $resource = null;
        $data = $this->stripeService->stripePayment($stripeParameters, $product);
        if($data){
            $data_ = $data['charges']['data'][0];
            $resource = [
                'stripeBrand'=> $data_['payment_method_details']['card']['brand'],
                'stripeLast4'=> $data_['payment_method_details']['card']['last4'],
                'stripeId'=> $data_['id'],
                'stripeStatus'=> $data_['status'],
                'stripeToken'=> $data_['client_secret'],
            ];
        }
        return $resource;
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
