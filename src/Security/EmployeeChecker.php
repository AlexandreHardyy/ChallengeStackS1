<?php

namespace App\Security;

use App\Entity\Employee as AppEmployee;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EmployeeChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $employee): void
    {
        if (!$employee instanceof AppEmployee) {
            return;
        }

        if (!$employee->isVerified()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Please verify your email address.');
        }

        
        if ($employee->isIsBanned()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your user account no longer exists.');
        }
    }

    
    public function checkPostAuth(UserInterface $employee): void
    {
        if (!$employee instanceof AppEmployee) {
            return;
        }

        // user account is expired, the user may be notified
        if ($employee->isIsBanned()) {
            throw new AccountExpiredException('...');
        }
    }
}