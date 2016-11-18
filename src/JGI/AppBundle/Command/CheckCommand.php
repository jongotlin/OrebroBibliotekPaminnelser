<?php

namespace JGI\AppBundle\Command;

use JGI\AppBundle\Service\EmailSender;
use JGI\AppBundle\Service\Scraper;
use JGI\AppBundle\Service\SlackBot;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends ContainerAwareCommand
{
    /**
     * @var \JGI\AppBundle\Service\EmailSender
     */
    protected $emailSender;

    /**
     * @var \JGI\AppBundle\Service\Scraper
     */
    protected $scraper;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var SlackBot
     */
    protected $slackBot;

    /**
     * @param EmailSender $emailSender
     * @param Scraper $scraper
     * @param array $credentials
     */
    public function __construct(EmailSender $emailSender, Scraper $scraper, array $credentials, SlackBot $slackBot)
    {
        $this->emailSender = $emailSender;
        $this->scraper = $scraper;
        $this->credentials = $credentials;
        $this->slackBot = $slackBot;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('check');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = [];
        $minDaysLeft = 1000;
        foreach ($this->credentials as $userData) {
            $output->writeln(sprintf('Logging in as %s', $userData[0]));
            $user = $this->scraper->scrape($userData[0], $userData[1], $userData[2]);
            if ($user->getNextBookToReturn() && $user->getNextBookToReturn()->getDaysLeft() < $minDaysLeft) {
                $minDaysLeft = $user->getNextBookToReturn()->getDaysLeft();
            }
            $users[] = $user;
        }

        if ($minDaysLeft < 7 || (new \DateTime())->format('N') == 1) {
            try {
                $output->writeln('Sending email');
                $this->emailSender->sendEmail($users);
            } catch (\Exception $e) {
                $output->writeln(sprintf('Failed sending email: %s', $e->getMessage()));
            }

            try {
                $output->writeln('Sending to Slack');
                $this->slackBot->send($users);
            } catch (\Exception $e) {
                $output->writeln(sprintf('Failed sending to Slack: %s', $e->getMessage()));
            }


        } else {
            $output->writeln('No late books');
        }
    }
}
