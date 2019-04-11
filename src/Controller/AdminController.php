<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\TrickEditContentType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

    /**
     * AdminController constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/")
     * @param TrickRepository $trickRepo
     * @param CategoryRepository $categoryRepo
     * @param UserRepository $userRepo
     * @return array
     * @Template()
     */
    public function index(TrickRepository $trickRepo, CategoryRepository $categoryRepo, UserRepository $userRepo)
    {
        $tricks = $trickRepo->findAll();
        $categoryForm = $this->createForm(CategoryType::class, new Category(), ['action' => $this->generateUrl('app_admin_addcategory')]);
        $categories = $categoryRepo->findAll();
        $users = $userRepo->findAll();

        return [
            'tricks' => $tricks,
            'categories' => $categories,
            'users' => $users,
            'categoryForm' => $categoryForm->createView(),
        ];
    }


    /**
     * @Route("/trick/delete/{slug}")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editTrick(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickEditContentType::class, $trick)
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
     * @param Request $request
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

    /**
     * @Route("/category/edit/{id}")
     * @param Category $category
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function editCategory(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();
            $this->addFlash('success', 'flash.editCategory.success');

            return $this->redirectToRoute('app_admin_index');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/user/delete/{slug}")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(User $user)
    {
        $this->manager->remove($user);
        $this->manager->flush();
        $this->addFlash('success', 'flash.user.delete.success');

        return $this->redirectToRoute('app_admin_index');
    }

    /**
     * @Route("/user/{slug}/role/{role}")
     * @ParamConverter("user", options={"mapping": {"slug": "slug"}})
     * @param $role
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function manageRoleUser($role, User $user)
    {
        $roles[] = $role;
        $user->setRoles($roles);
        $this->manager->flush();
        $this->addFlash('success', 'flash.user.role.management.success');

        return $this->redirectToRoute('app_admin_index');
    }
}
