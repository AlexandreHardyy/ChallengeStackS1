<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Services\StripeService;
use Stripe\Exception\ApiErrorException;


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
     /**
     * @var StripeService
     */
    protected StripeService $stripeService;

    /**
     * @param ManagerRegistry $registry
     * @param StripeService $stripeService
     */
    public function __construct(ManagerRegistry $registry, StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
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
        $qb->select('p.id, p.title, p.slug, p.price, p.description, p.image, od.Price as orderPrice')
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

    public function createOrder(array $resource, int $price, User $user){
        $order = new Order();
        $order->setOwner($user);
        $order->setTotalPaid($price);
        $order->setBrandStripe($resource['stripeBrand']);
        $order->setIdChargeStripe($resource['stripeId']);
        $order->setLast4Stripe($resource['stripeLast4']);
        $order->setStatusStripe($resource['stripeStatus']);
        $order->setStripeToken($resource['stripeToken']);

        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function findByLast30Days(): array
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o.id, o.totalPaid, o.createdAt')
            ->where('o.createdAt >= :date')
            ->setParameter('date', new \DateTime('-30 days'));

        return $qb->getQuery()->getResult();
    }

    public function stripeRefund(array $stripeParameters): ?array
    {
        $refund = $this->stripeService->stripeRefund($stripeParameters);
        if(!empty($refund)){
            $resource = [
                'charge' => $refund['chargeId'],
                'amount' => $refund['amount'],
            ];
        }
        return $resource;
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
