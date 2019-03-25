<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\MailType;
use App\Form\RegistrationType;
use App\Form\SecurityPasswordUpdateType;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\TrickService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @throws \Exception
     * @Template()
     */
    public function registration(Request $request, TrickService $trickService)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $trickService->generateToken();
            $user->setToken($token);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->mailService->sendRegistrationConfirm($user);
            $this->addFlash(
                'success',
                'flash.user.registration.success');

            return $this->redirectToRoute('app_security_login');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/activate/account/{user}/{token}")
     * @ParamConverter("user", options={"mapping": {"user": "email"}})
     * @param User $user
     * @param $token
     */
    public function accountActivate(User $user, $token)
    {
        if ($token == $user->getToken()) {
            $role[] = 'ROLE_USER';
            $user->setRoles($role);
            $this->manager->flush();
            $this->addFlash('success', 'flash.user.activate.success');

            return $this->redirectToRoute('app_security_login');
        }
        $this->addFlash('success', 'flash.user.activate.danger');

        return $this->redirectToRoute('app_security_login');
    }

    /**
     * @Route("/update/password/{email}/{token}")
     * @ParamConverter("user", options={"mapping": {"email": "email"}})
     * @param User $user
     * @param $token
     * @Template()
     */
    public function updatePassword(User $user,Request $request, $token)
    {
        if ($token == $user->getToken()) {
            $form = $this->createForm(SecurityPasswordUpdateType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                $this->addFlash('success','flash.user.password.edit.success');

                return $this->redirectToRoute('app_security_login');
            }

            return ['form' => $form->createView()];
        }
        $this->addFlash('danger', 'flash.user.updatePassword.danger');

        return $this->redirectToRoute('app_security_login');
    }

    /**
     * @Route("/mail/confirm")
     * @param Request $request
     * @param UserRepository $userRepo
     */
    public function sendUpdatePassword(Request $request, UserRepository $userRepo)
    {

        $user = new User();
        $form = $this->createForm(MailType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $userToSend = $userRepo->findOneBy(['email' => $user->getEmail()]);
            if ($userToSend) {
                $this->mailService->sendUpdatePassword($userToSend);
                $this->addFlash('success', 'flash.send.updatePassword.success');

                return $this->redirectToRoute('app_security_login');
            }
            $this->addFlash('danger', 'flash.send.updatePassword.danger');

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('/security/mail_confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
