<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/edit")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function edit(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été mis à jour !');

            return $this->redirectToRoute('app_user_profile');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/user/profile")
     * @Route("/user/profile/{slug}")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function profile(?User $user)
    {
        return ['user' => $user = ($user ? $user : $this->getUser())];
    }


}
