<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick")
     * @Template()
     */
    public function index()
    {
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findAll();

        return ['tricks' => $tricks];
    }

    /**
     * @Route("/trick/add")
     * @Template()
     */
    public function addTrick(Request $request, ObjectManager $manager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash('success', 'flash.trick.add.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/trick/{slug}")
     * @param Trick $trick
     * @Template()
     */
    public function show(Trick $trick)
    {
        return ['trick' => $trick];
    }
}
