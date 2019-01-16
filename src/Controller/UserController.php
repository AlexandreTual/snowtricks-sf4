<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\PasswordUpdateType;
use App\Form\ProfileType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['form' => $form->createView(), 'last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Permet de déconnecter un utilisateur
     * @Route("/logout", name="user_logout")
     * @return void
     */
    public function logout()
    {

    }

    /**
     * Permet d'afficher un formulaire d'inscription
     * @Route("/register", name="user_register")
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
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

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de gérer le profile utilisateur
     * @Route("/user/profile", name="user_profile")
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été mis à jour !');
        }

        return $this->render('user/profile.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Permet de gérer le mot de passe utilisateur
     * @Route("/user/password-update", name="user_password")
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $form = $this->createForm(PasswordUpdateType::class);
        $form->handleRequest($request);
        $user = $this->getUser();
        if (!$userPasswordEncoder->isPasswordValid($user,$form->get('oldPassword')->getData())) {
            $this->addFlash(
                'danger',
                "Vous n'avez pas tapé correctement votre mot de passe actuel.");
            $this->redirectToRoute('user_password');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $userPasswordEncoder->encodePassword($user,$form->get('password')->getData());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre nouveau mot de passe à bien été enregistré !");
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
