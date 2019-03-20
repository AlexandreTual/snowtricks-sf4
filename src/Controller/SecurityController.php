<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Service\MailService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $mailService;
    private $manager;

    public function __construct(MailService $mailService, ObjectManager $manager)
    {
        $this->mailService = $mailService;
        $this->manager = $manager;
    }

    /**
     * @Route("/login")
     * @Template()
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginType::class, [$lastUsername]);
        $error = $authenticationUtils->getLastAuthenticationError();

        return ['form' => $form->createView(), 'error' => $error];
    }

    /**
     * @Route("/logout")
     */
    public function logout()
    {
    }



    /**
     * @Route("/registration")
     * @Template()
     */
    public function registration(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipient = $form->get('email')->getData();
            $name = $form->get('firstName')->getData();

            $this->manager->persist($user);
            $this->manager->flush();
            $this->mailService->sendRegistrationConfirm($recipient, $name);
            $this->addFlash(
                'success',
                'flash.user.registration.success');

            return $this->redirectToRoute('app_security_login');
        }

        return ['form' => $form->createView()];
    }
}
