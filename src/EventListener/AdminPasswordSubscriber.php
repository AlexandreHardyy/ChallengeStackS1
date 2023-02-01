<?php

// src/EventListener/DatabaseActivitySubscriber.php
namespace App\EventListener;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AdminPasswordSubscriber implements EventSubscriberInterface
{
   
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $employee = $args->getObject();
        if (!$employee instanceof Employee) return;
        $this->encodePassword($employee);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $employee = $args->getObject();
        if (!$employee instanceof Employee) return;
        $this->encodePassword($employee);
    }

    public function encodePassword(Employee $employee)
    {
        if ($employee->getPlainPassword() === null) {
           return;
        }
        $employee->setPassword($this->hasher->hashPassword(
            $employee,
            $employee->getPlainPassword(),
        ));
    }
}