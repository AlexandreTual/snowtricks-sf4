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

    public function sendRegistrationConfirm($recipient, $name)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('tual.alexandre@gmail.com')
            ->setTo($recipient)
            ->setBody(
                $this->env->render('/emails/registration.html.twig', [
                    'email' => $recipient,
                    'name' => $name,
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
