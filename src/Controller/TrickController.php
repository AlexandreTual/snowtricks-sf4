<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\TrickEditMediaType;
use App\Form\TrickType;
use App\Form\TrickEditTextType;
use App\Form\CommentType;
use App\Service\TrickService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TrickController extends AbstractController
{
    private $trickService;
    private $manager;

    public function __construct(TrickService $trickService, ObjectManager $manager)
    {
        $this->trickService = $trickService;
        $this->manager = $manager;
    }

    /**
     * @Route("/trick/category/{name}")
     * @param $category
     * @Template()
     */
    public function trickByCategory(Category $category)
    {
       $tricks = $this->manager->getRepository(Trick::class)->findBy(['category' => $category]);

        return [
            'tricks' => $tricks,
            'category' => $category,
        ];
    }

    /**
     * @param Request $request
     * @param ObjectManager $manager
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/trick/add")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function add(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());
            $this->manager->persist($trick);
            $this->manager->flush();
            $this->addFlash('success', 'flash.trick.add.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/trick/edit/{slug}")
     * @param Trick $trick
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     * @Template()
     */
    public function edit(Request $request, Trick $trick)
    {
        $form = $this->createForm(TrickEditTextType::class, $trick)
            ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'flash.trick.update.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick
        ];
    }

    /**
     * @Route("/trick/edit/media/{slug}")
     * @param $slug
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("(is_granted('ROLE_USER') and (user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN'))", message="functionality.access.denied")
     * @Template()
     */
    public function editMedia(Trick $trick, Request $request)
    {
        $newTrick = new Trick();
        $form = $this->createForm(TrickEditMediaType::class, $newTrick)
            ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->trickService->editMedia($form, $trick);
            $this->addFlash('success', 'flash.trick.update.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick
        ];
    }

    /**
     * @Route("/trick/delete-media/{mediaType}/{trick_slug}/{mediaId}")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @ParamConverter("image", options={"id": "image_id"})
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     */
    public function removeMedia(Trick $trick, $mediaType, $mediaId )
    {
        $this->trickService->removeMedia($trick, $mediaType, $mediaId);
        $this->addFlash('success', 'flash.' . $mediaType . '.delete.success');

        return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/{slug}/")
     * @param Trick $trick
     * @return array
     * @Template()
     */
    public function show(Trick $trick)
    {
        $form = $this->createForm(CommentType::class);
        return [
            'trick' => $trick,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/trick/delete/{slug}")
     * @param Trick $trick
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Trick $trick)
    {
        $this->manager->remove($trick);
        $this->manager->flush();
        $this->addFlash('success', 'flash.trick.delete.success');

        return $this->redirectToRoute('app_trick_index');
    }
}
