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
     * @Route("/user/password-update")
     * @IsGranted("ROLE_USER")
     * @Template()
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

                return $this->redirectToRoute('app_security_updatepassword');
            }
            $hash = $userPasswordEncoder->encodePassword($user, $form->get('password')->getData());
            $user->setHash($hash);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre nouveau mot de passe à bien été enregistré !");

            return $this->redirectToRoute('app_user_show');
        }
        return ['form' => $form->createView()];
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
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée, vous pouvez à présent vous connecter !');

            return $this->redirectToRoute('app_security_login');
        }

        return ['form' => $form->createView()];
    }
}
