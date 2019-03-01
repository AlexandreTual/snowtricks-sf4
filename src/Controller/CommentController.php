<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Timestampable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/add/{slug}")
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @throws \Exception
     * @return void
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function add(Request $request, ObjectManager $manager, Trick $trick)
    {
        $comment = new Comment();
        $date = new Timestampable();
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser())
                ->setTrick($trick)
                ->setDate($date);
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'comment.add.success');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }
    }

    /**
     * @Route("/comment/delete/{trick_slug}/{comment_id}")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @ParamConverter("comment", options={"id": "comment_id"})
     * @param Trick $trick
     * @param Comment $comment
     * @param ObjectManager $manager
     * @Security("(is_granted('ROLE_USER') and user === comment.getUser()) or is_granted('ROLE_ADMIN')")
     * @return void
     */
    public function delete(Trick $trick, Comment $comment, ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'flash.comment.delete.success');

        return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/comment/list/{trick_slug}/{offset}", condition="request.isXmlHttpRequest()")
     * @ParamConverter("trick", options={"mapping": {"trick_slug": "slug"}})
     * @param Trick $trick
     * @param ObjectManager $manager
     */
    public function getComments(Trick $trick, $offset, ObjectManager $manager)
    {
        $comments = $manager->getRepository(Comment::class)->findBy(['trick' => $trick->getId()],['id' => 'DESC'], 10, $offset);

        return $this->render('/comment/_comments.html.twig', ['trick' => $trick, 'comments' => $comments]);
    }
}
