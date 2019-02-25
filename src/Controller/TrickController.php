<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
     * @Template()
     */
    public function add(Request $request, ObjectManager $manager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $trick->getImages();
            foreach ($images as $image){
                $file = $image->getLink();
                $caption = $image->getCaption();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $fileName
                    );
                    $image->setLink($fileName)
                    ->setCaption($caption);
                } catch (FileException $e) {
                    dd($e);
                }
            }
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
     * @Template()
     */
    public function edit(Trick $trick, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isSubmitted()) {
            $manager->flush();
            $this->addFlash('success', 'flash.trick.update.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return ['form' => $form->createView(), 'trick' => $trick];
    }

    /**
     * @Route("/trick/{slug}/")
     * @param Trick $trick
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
}
