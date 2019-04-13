<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\EmailValidationType;
use App\Form\RegistrationType;
use App\Form\ForgotPasswordValidationType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @param AuthenticationUtils $authenticationUtils
     * @return array
     * @Template()
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginType::class, [$lastUsername]);
        $error = $authenticationUtils->getLastAuthenticationError();

        return [
            'form' => $form->createView(),
            'error' => $error,
        ];
    }

    /**
     * @Route("/logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setHash($encoder->encodePassword($user, $user->getHash()));
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function accountActivate(User $user, $token)
    {
        if ($token === $user->getToken()) {
            $user->setRoles(['ROLE_USER']);
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
     * @param Request $request
     * @param $token
     * @param UserPasswordEncoderInterface $encoder
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function updatePassword(User $user, Request $request, $token, UserPasswordEncoderInterface $encoder)
    {
        if ($token == $user->getToken()) {
            $form = $this->createForm(ForgotPasswordValidationType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setHash($encoder->encodePassword($user, $user->getHash()));
                $user->generateToken();
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function sendUpdatePassword(Request $request, UserRepository $userRepo)
    {
        $user = new User();
        $form = $this->createForm(EmailValidationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $userToSend = $userRepo->findOneBy(['email' => $user->getEmail()]);
            if ($userToSend) {
                if (['ROLE_NO_ACTIVATE'] == $userToSend->getRoles()) {
                    $this->addFlash('danger', 'flash.user.updatePassword.before.activate');

                    return $this->redirectToRoute('app_security_login');
                }
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
