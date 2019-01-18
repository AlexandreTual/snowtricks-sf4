<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login")
     * @Template()
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $form = $this->createForm(LoginType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return ['form' => $form->createView(), 'last_username' => $lastUsername, 'error' => $error];
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
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée, vous pouvez à présent vous connecter !');

            return $this->redirectToRoute('app_security_login');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/user/password-update")
     * @Template()
     * @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $form = $this->createForm(PasswordUpdateType::class);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$userPasswordEncoder->isPasswordValid($user,$form->get('oldPassword')->getData())) {
                $this->addFlash(
                    'danger',
                    "Vous n'avez pas correctement confirmé votre mot de passe actuel.");

                return $this->redirectToRoute('app_security_update_password');
            } else {
                $hash = $userPasswordEncoder->encodePassword($user,$form->get('password')->getData());
                $user->setHash($hash);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre nouveau mot de passe à bien été enregistré !");

                return $this->redirectToRoute('app_user_show');
            }
        }
        return ['form' => $form->createView()];
    }
}
