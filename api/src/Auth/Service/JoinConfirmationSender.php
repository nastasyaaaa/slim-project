<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\Email;
use Swift_Mailer;
use Swift_Message;

class JoinConfirmationSender implements IJoinConfirmationSender
{
    private Swift_Mailer $mailer;
    private array $from;

    /**
     * JoinConfirmationSender constructor.
     * @param \Swift_Mailer $mailer
     * @param array $from
     */
    public function __construct(Swift_Mailer $mailer, array $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, Token $token): void
    {
        $message = (new Swift_Message('Confirmation'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody('http://localhost:8082/join/confirm?' . http_build_query([
                    'token' => $token->getValue()
                ]));


        if ($this->mailer->send($message) === 0) {
            throw new \RuntimeException('Unable to send email.');
        }
    }


}