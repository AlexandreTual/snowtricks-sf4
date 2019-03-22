<?php

namespace App\Service;

use Twig\Environment;

class MailService
{
    private $mailer;
    private  $env;

    public function __construct(\Swift_Mailer $mailer, Environment $environment)
    {
        $this->mailer = $mailer;
        $this->env = $environment;
    }

    public function sendRegistrationConfirm($user)
    {
        $message = (new \Swift_Message('Snowtricks'))
            ->setFrom('tual.alexandre@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->env->render('/emails/registration.html.twig', [
                    'user' => $user,
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
