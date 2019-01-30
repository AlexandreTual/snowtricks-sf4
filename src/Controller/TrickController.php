<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\LinkVideoValidation;
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
    public function add(Request $request, ObjectManager $manager, LinkVideoValidation $videoValidation)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medias = $trick->getMedia();
            // setter la coverImage si elle n'existe pas.
            if (null == $trick->getCoverImage()) {
                if (isset($medias)) { 
                    foreach ($medias as $media) {
                        if ('image' == $media->getType()) {
                            $trick->setCoverImage($media->getLink());
                            break;
                        } 
                    }
                }
                $trick->setCoverImage('http://lorempixel.com/1000/600');
            }
            
            // check la validité du fomat des liens vidéos
            if (isset($medias)) {
                foreach ($medias as $media) {
                    if ('video' == $media->getType()) {
                        $link = $media->getLink();
                        if (!$videoValidation->isValid($link)) {
                            $this->addFlash('danger', 'flash.trick.link.danger');
                            
                            return $this->redirectToRoute('app_trick_add', ['slug' => $trick->getSlug()]);
                        }
                    }
                }
            }
            dump($trick);
            die;
            
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
    public function edit(Trick $trick, Request $request, ObjectManager $manager, LinkVideoValidation $videoValidation)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isSubmitted()) {
            $medias = $trick->getMedia();
            
            foreach ($medias as $media) {
                $link = $media->getLink();
                if ($videoValidation->isValid($link)) {
                    $link = $videoValidation->formatLink($link);
                    $media->setLink($link);
                } else {
                    $this->addFlash('danger', 'flash.trick.link.danger');
                    
                    return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
                }
            }
            
            $manager->flush();
            $this->addFlash('success', 'flash.trick.update.success');
            
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }
        
        return ['form' => $form->createView(), 'trick' => $trick];
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
