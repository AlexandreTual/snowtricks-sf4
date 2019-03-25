<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddAvatarType;
use App\Form\PasswordUpdateType;
use App\Form\ProfileType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/edit")
     * @Template()
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash(
                'success',
                'flash.user.profile.edit.success');

            return $this->redirectToRoute('app_user_profile', ['slug' => $user->getSlug()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/profile/{slug}")
     * @Template()
     */
    public function profile(User $user)
    {
        return ['user' => $user];
    }

    /**
     * @Route("/add-avatar")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function addAvatar(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(AddAvatarType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'flash.add.avatar.success');

            return $this->redirectToRoute('app_user_profile', ['slug' => $user->getSlug() ]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/password-update")
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $form = $this->createForm(PasswordUpdateType::class);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$userPasswordEncoder->isPasswordValid($user,$form->get('oldPassword')->getData())) {
                $this->addFlash(
                    'danger',
                    'flash.user.password.edit.danger');

                return $this->redirectToRoute('app_user_updatepassword');
            }
            $this->manager->flush();
            $this->addFlash(
                'success',
                "flash.user.password.edit.success");

            return $this->redirectToRoute('app_user_profile', ['slug' => $user->getSlug()]);
        }

        return ['form' => $form->createView()];
    }
}
