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
        $images = $this->getDoctrine()->getRepository(Image::class)->findAll();
        return [
            'tricks' => $tricks,
            'images' => $images,
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
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     * @Template()
     */
    public function edit(Request $request,ObjectManager $manager, Trick $trick)
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
     * @Security("(is_granted('ROLE_USER') and (user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN'))", message="functionality.access.denied")
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
     * @Route("/trick/delete-media/{mediaType}/{trick_slug}/{mediaId}")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @ParamConverter("image", options={"id": "image_id"})
     * @param Trick $trick
     * @param Image $image
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     */
    public function removeMedia(Trick $trick, ObjectManager $manager, $mediaType, $mediaId )
    {
        if ('image' == $mediaType) {
            $image = $manager->getRepository(Image::class)->findOneById($mediaId);
            $trick->removeImage($image);
            $this->addFlash('success', 'flash.image.delete.success');
        }
        if ('video' == $mediaType) {
            $video = $manager->getRepository(Video::class)->findOneById($mediaId);
            $trick->removeVideo($video);
            $this->addFlash('success', 'flash.video.delete.success');
        }
        $manager->flush();

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
     * @Security("(is_granted('ROLE_USER') and user.getId() === trick.getUser().getId()) or is_granted('ROLE_ADMIN')", message="functionality.access.denied")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Trick $trick, ObjectManager $manager)
    {
            $manager->remove($trick);
            $manager->flush();

        return $this->redirectToRoute('app_trick_index');
    }
}
