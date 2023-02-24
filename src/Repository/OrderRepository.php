<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllProductsByOrderId(int $orderId): array
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('p.id, p.title, p.price, p.description, p.image, od.Price as orderPrice')
            ->innerJoin('o.orderDetails', 'od')
            ->innerJoin('od.productId', 'p')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId);

        return $qb->getQuery()->getResult();
    }

    public function findOrderWithProducts(int $orderId): ?Order
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o, od, p')
            ->innerJoin('o.orderDetails', 'od')
            ->innerJoin('od.productId', 'p')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOrderWithProductsAndHistory(int $orderId): ?Order
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o, od, p, oh')
            ->innerJoin('o.orderDetails', 'od')
            ->innerJoin('od.productId', 'p')
            ->innerJoin('o.orderHistories', 'oh')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByLast30Days(): array
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o.id, o.totalPaid, o.createdAt')
            ->where('o.createdAt >= :date')
            ->setParameter('date', new \DateTime('-30 days'));

        return $qb->getQuery()->getResult();
    }
//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
