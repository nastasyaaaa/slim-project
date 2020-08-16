<?php

namespace App\Console\Commands;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\IJoinConfirmationSender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailerCheckCommand extends Command
{
    private IJoinConfirmationSender $sender;

    public function __construct(IJoinConfirmationSender $sender, string $name = null)
    {
        parent::__construct($name);
        $this->sender = $sender;
    }

    protected function configure()
    {
        $this->setName('mailer:check');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Sending mail...</info>');

        $this->sender->send(
            new Email('nasya12@app.test'),
            new Token(Uuid::uuid4(), new \DateTimeImmutable())
        );

        $output->writeln('<info>Done!</info>');

        return 0;
    }
}