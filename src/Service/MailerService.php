<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function sendEmail(
        $from,
        $to,
        $content,
        $subject,
        $personne
        ): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
//            ->text('Sending emails is fun again!')
            ->htmlTemplate('emails/notif-mail.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'personne' => $personne
            ]);
         $this->mailer->send($email);

        // ...
    }
}