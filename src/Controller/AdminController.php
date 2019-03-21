<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\CategoryType;
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
        $tricks = $this->manager->getRepository(Trick::class)->findBy([], [], 15);
        $categoryForm = $this->createForm(CategoryType::class, new Category(), ['action' => $this->generateUrl('app_admin_addcategory')]);
        $categories = $this->manager->getRepository(Category::class)->findAll();

        return [
            'tricks' => $tricks,
            'categories' => $categories,
            'categoryForm' => $categoryForm->createView(),
        ];
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


    /**
     * @Route("/category/add")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();
            $this->addFlash('success', 'flash.addCategory.success');
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
