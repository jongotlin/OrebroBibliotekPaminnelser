<?php

namespace JGI\AppBundle\Service;

use Goutte\Client;
use JGI\AppBundle\Model\User;

class SlackBot
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * @param Client $client
     * @param string $url
     */
    public function __construct(Client $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * @param User[] $users
     */
    public function send($users)
    {
        if (!$this->url) {
            return;
        }

        $rows = [];
        $books = [];
        foreach ($users as $user) {
            foreach ($user->getBooks() as $book) {
                $books[] = $book;
            }
        }

        usort($books, function($a, $b) {
            if ($a->getDaysLeft() == $b->getDaysLeft()) {
                return 0;
            }

            return $a->getDaysLeft() > $b->getDaysLeft() ? 1 : -1;
        });

        foreach ($books as $book) {
            $days = $book->getDaysLeft();
            if ($days == 0) {
                $rows[] = sprintf('*%s* - skall lämnas tillbaka idag', $book->getTitle());
            } elseif ($days == 1) {
                $rows[] = sprintf('*%s* - skall lämnas tillbaka imorgon', $book->getTitle());
            } elseif ($days == -1) {
                $rows[] = sprintf('*%s* - skulle lämnas tillbaka igår', $book->getTitle());
            } elseif ($days < 0) {
                $rows[] = sprintf('*%s* - skulle lämnas tillbaka för %s dagar sedan', $book->getTitle(), abs($days));
            } elseif ($days > 0) {
                $rows[] = sprintf('*%s* - ska lämnas tillbaka inom %s dagar', $book->getTitle(), $days);
            }
        }

        $payload = [
            'text' => implode("\n", $rows),
        ];
        $encoded = json_encode($payload);

        $this->client->getClient()->post($this->url, ['body' => $encoded]);
    }
}

