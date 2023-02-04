<?php

namespace App\Controller\Back;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UpdateEmployeeFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class TeamController extends AbstractController
{
   
    #[Route('/team', name: 'app_team')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();
        return $this->render('back/team/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    
   
    #[Route('/team/{id}/edit', name: 'app_team_edit')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function editOne(Employee $employee, EmployeeRepository $employeeRepository, Request $request): Response
    {
        
        $form = $this->createForm(UpdateEmployeeFormType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->save($employee, true);
            $this->addFlash('success', 'Employee updated!');
            return $this->redirectToRoute('admin_app_team');
        }

        return $this->renderForm('back/team/show.html.twig', [
            'updateEmployeeForm' => $form
        ]);
    }
    
    #[Route('/team/banned/{id}', name: 'app_team_banned')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function bannedOne(Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if($employee->isIsBanned() == true)
        {
            $employee->setIsBanned(false);
            $employeeRepository->save($employee, true);
            $this->addFlash('warning', 'Employee unbanned!');
            return $this->redirectToRoute('admin_app_team');
        }

        $employee->setIsBanned(true);
        $employeeRepository->save($employee, true);
        $this->addFlash('success', 'Employee banned!');
        return $this->redirectToRoute('admin_app_team');
    }

}
