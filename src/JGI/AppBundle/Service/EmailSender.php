<?php

namespace JGI\AppBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class EmailSender
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $recipients;

    /**
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     * @param string $from
     * @param array $recipients
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $from, array $recipients)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->from = $from;
        $this->recipients = $recipients;
    }

    /**
     * @param \JGI\AppBundle\Model\User[] $users
     */
    public function sendEmail(array $users)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Bibliotekslån')
            ->setFrom($this->from)
            ->setTo($this->recipients)
            ->setBody(
                $this->templating->render(
                    'JGIAppBundle::email.html.twig',
                    ['users' => $users]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
