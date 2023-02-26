<?php
namespace App\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataToTwig extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_data', [$this, 'getData']),
        ];
    }

    public function getData($entityClass)
    {
        // Fetch the SQL data using Doctrine ORM or DBAL
        $repository = $this->entityManager->getRepository($entityClass);
        $data = $repository->findAll();
        // Return the data as an array
        return $data;
    }
}
