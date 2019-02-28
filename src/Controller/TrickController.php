<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickEditMediaType;
use App\Form\TrickType;
use App\Form\TrickEditTextType;
use App\Form\CommentType;
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
    /**
     * @Route("/trick")
     * @Template()
     */
    public function index()
    {
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findBy([], ['id' => 'DESC']);

        return ['tricks' => $tricks];
    }

    /**
     * @param Request $request
     * @param ObjectManager $manager
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/trick/add")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function add(Request $request, ObjectManager $manager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash('success', 'flash.trick.add.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/trick/edit/{slug}")
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="functionality.access.denied")
     * @Template()
     */
    public function edit(Trick $trick, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(TrickEditTextType::class, $trick)
            ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
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
     * @param ObjectManager $manager
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="functionality.access.denied")
     * @Template()
     */
    public function editMedia($slug, Trick $trick, Request $request, ObjectManager $manager )
    {
        $newTrick = new Trick();
        $form = $this->createForm(TrickEditMediaType::class, $newTrick)
            ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('images')->getData() as $dataImage) {
                $image = new Image();
                $image->setLink($dataImage->getLink())
                    ->setCaption($dataImage->getCaption());
                $trick->addImage($image);
            }
            foreach ($form->get('videos')->getData() as $dataVideo) {
                $video = new Video();
                $video->setTag($dataVideo->getTag());
                $trick->addVideo($video);
                $videos = $trick->getVideos();
                dd($videos);
            }
            $manager->flush();
            $this->addFlash('success', 'flash.trick.update.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $slug]);
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick
        ];
    }

    /**
     * @Route("/trick/delete-image/{trick_slug}/{image_id}")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @ParamConverter("image", options={"id": "image_id"})
     * @param Trick $trick
     * @param Image $image
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="functionality.access.denied")
     */
    public function removeImage(Trick $trick, Image $image, ObjectManager $manager)
    {
        $trick->removeImage($image);
        $manager->flush();
        $this->addFlash('success', 'flash.image.delete.success');

        return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/trick/delete-video/{trick_slug}/{video_id}")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @ParamConverter("video", options={"id": "video_id"})
     * @param Trick $trick
     * @param Video $video
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="functionality.access.denied")
     */
    public function removeVideo(Trick $trick, Video $video, ObjectManager $manager)
    {
        $trick->removeVideo($video);
        $manager->flush();
        $this->addFlash('success', 'flash.video.delete.success');

        return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/trick/{slug}/")
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
     * @param ObjectManager $manager
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="functionality.access.denied")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Trick $trick, ObjectManager $manager)
    {
        if ($trick->getUser() == $this->getUser()) {
            $manager->remove($trick);
            $manager->flush();
        }

        return $this->redirectToRoute('app_trick_index');
    }
}
