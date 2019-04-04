<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\EditImageType;
use App\Form\EditVideoType;
use App\Form\TrickEditMediaType;
use App\Form\TrickType;
use App\Form\TrickEditContentType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
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
    public function trickByCategory(Category $category, TrickRepository $trickRepo)
    {
       $tricks = $trickRepo->findBy(['category' => $category]);

        return [
            'tricks' => $tricks,
            'category' => $category,
        ];
    }

    /**
     * @Route("/trick/add")
     * @param Request $request
     * @param ObjectManager $manager
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
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
        $form = $this->createForm(TrickEditContentType::class, $trick)
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
     * @Route("/trick/{slug}/edit/image/{id}/")
     * @ParamConverter("trick", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("image", options={"id": "id"})
     * @param Image $image
     * @param Trick $trick
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function editImage(Image $image, Trick $trick, Request $request)
    {
        $newImage = new Image();
        $form = $this->createForm(EditImageType::class, $newImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->removeImage($image);
            $trick->addImage($newImage);
            $this->manager->flush();

            $this->addFlash('success', 'flash.edit.image.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return [
            'form' => $form->createView(),
            'image' => $image,
        ];
    }

    /**
     * @Route("/trick/{slug}/edit/video/{id}/")
     * @ParamConverter("trick", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("video", options={"id": "id"})
     * @param Video $video
     * @param Trick $trick
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function editVideo(Video $video, Trick $trick, Request $request)
    {
        $newVideo = new Video();
        $form = $this->createForm(EditVideoType::class, $newVideo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->removeVideo($video);
            $trick->addVideo($newVideo);
            $this->manager->flush();

            $this->addFlash('success', 'flash.edit.video.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return [
            'form' => $form->createView(),
            'video' => $video,
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
