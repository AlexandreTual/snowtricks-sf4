<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/profile")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été mis à jour !');

            return $this->redirectToRoute('app_user_show');
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/account")
     * @Template()
     * @IsGranted("ROLE_USER")
     */
    public function show()
    {
        return ['user' => $this->getUser()];
    }


}
