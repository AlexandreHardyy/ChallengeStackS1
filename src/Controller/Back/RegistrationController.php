<?php

namespace App\Controller\Back;

use App\Entity\Employee;
use App\Form\AdminRegistrationFormType;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\EmployeeRepository;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/team/add', name: 'app_register')]
    public function register(Request $request, 
    UserAuthenticatorInterface $userAuthenticator, 
    AppCustomAuthenticator $authenticator, 
    EmployeeRepository $employeeRepository): Response
    {
        $employee = new Employee();
        $form = $this->createForm(AdminRegistrationFormType::class, $employee);
        $form->handleRequest($request);

      
        if ($form->isSubmitted() && $form->isValid()) {
            
            // encode the plain password
            $employee->setRoles($form->get('roles')->getData());
            $employeeRepository->save($employee, true);

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('admin_app_verify_email', $employee,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@vgcreator.fr', 'Confirmation de votre email'))
                    ->to($employee->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('back/registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Your account has been created. Please check your email to confirm your email address.');
            return $this->redirectToRoute('admin_app_login');
            /*
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );*/
        }

        return $this->render('back/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('admin_app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('admin_app_login');
    }
}
