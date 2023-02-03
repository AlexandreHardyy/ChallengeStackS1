<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateProfileType;
use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;

class UpdateProfileController extends AbstractController
{
    #[Route('/update/profile/', name: 'app_update_profile')]
    public function edit(EmployeeRepository $employeeRepository, Request $request): Response
    {
        $employee = $this->getUser();

        $form = $this->createForm(UpdateProfileType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->save($employee, true);
            $this->addFlash('success', 'Profile updated!');
            return $this->redirectToRoute('admin_app_default');
        }

        return $this->renderForm('back/update_profile/index.html.twig', [
            'updateProfileForm' => $form
        ]);
    }
}
