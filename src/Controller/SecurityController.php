<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
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
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['form' => $form->createView(), 'last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Permet de déconnecter un utilisateur
     * @Route("/logout", name="security_logout")
     * @return void
     */
    public function logout()
    {

    }

    /**
     * Permet d'afficher un formulaire d'inscription
     * @Route("/register", name="security_register")
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $roles = ['ROLE_USER'];
            $user->setHash($hash);
            $user->setRoles($roles);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée, vous pouvez à présent vous connecter !');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de gérer le mot de passe utilisateur
     * @Route("/user/password-update", name="security_password_update")
     * @IsGranted("ROLE_USER")
     * @return Response
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

                return $this->redirectToRoute('security_password_update');
            } else {
                $hash = $userPasswordEncoder->encodePassword($user,$form->get('password')->getData());
                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre nouveau mot de passe à bien été enregistré !");

                return $this->redirectToRoute('account_show');
            }
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
