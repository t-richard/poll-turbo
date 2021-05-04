<?php

namespace App\Command;

use App\Entity\Poll;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class PollCommand extends Command
{
    protected static $defaultName = 'app:poll';
    protected static $defaultDescription = 'Create a poll for next week';

    public function __construct(private ManagerRegistry $doctrine, private NotifierInterface $notifier)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $poll = new Poll();

        $poll
            ->setStartDate(new \DateTime('monday next week'))
            ->setEndDate(new \DateTime('friday next week'))
        ;

        $this->doctrine->getManager()->persist($poll);
        $this->doctrine->getManager()->flush();

        $message = new Notification('Quand es-tu présent au bureau la semaine prochaine ?');

        $this->notifier->send($message);

        return Command::SUCCESS;
    }
}
