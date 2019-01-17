<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Permet de gérer le profile utilisateur
     * @Route("/user/profile", name="user_profile")
     * @IsGranted("ROLE_USER")
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

            return $this->redirectToRoute('account_show');
        }

        return $this->render('user/profile.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Permet d'accéder à la page de l'utilisateur avec le slug
     * @Route("/account", name="account_show")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function show()
    {
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser()
        ]);
    }


}
