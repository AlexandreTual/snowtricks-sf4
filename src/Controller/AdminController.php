<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickEditTextType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function index()
    {
        $tricks = $this->manager->getRepository(Trick::class)->findAll();

        return ['tricks' => $tricks];
    }

    /**
     * @Route("/trick/delete/{slug}")
     * @param Trick $trick
     */
    public function deleteTrick(Trick $trick)
    {
        $this->manager->remove($trick);
        $this->manager->flush();
        $this->addFlash('success', 'flash.trick.delete.success');

        return $this->redirectToRoute('app_admin_index');
    }


    /**
     * @Route("/trick/edit/{slug}")
     * @param Trick $trick
     * @param Request $request
     */
    public function editTrick(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickEditTextType::class, $trick)
            ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'flash.trick.update.success');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }
}
