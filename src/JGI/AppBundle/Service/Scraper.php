<?php

namespace JGI\AppBundle\Service;

use Goutte\Client;
use JGI\AppBundle\Model\Book;
use JGI\AppBundle\Model\User;
use Symfony\Component\DomCrawler\Crawler;

class Scraper
{
    /**
     * @var \Goutte\Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $name
     * @param $username
     * @param $password
     *
     * @return User
     */
    public function scrape($name, $username, $password)
    {
        $crawler = $this->client->request('GET', 'http://bibliotek.orebro.se/login');
        $form = $crawler->selectButton('Logga in')->form();
        $this->client->submit($form, ['Username' => $username, 'Password' => $password]);

        $this->client->request('GET', 'http://bibliotek.orebro.se/api/loans');

        $content = (string)$this->client->getResponse()->getContent();

        $array = json_decode($content, true);

        $books = [];
        foreach ($array as $item) {
            $book = new Book();
            $book->setTitle($item['workTitle']);
            $book->setAuthor($item['workAuthor']);
            $book->setReturnDate(new \DateTime($item['returnDate']));
            $books[] = $book;
        }

        if ($books) {
            usort($books, function(Book $a, Book $b) {
                if ($a->getDaysLeft() == $b->getDaysLeft()) {
                    return 0;
                }
                return ($a->getDaysLeft() < $b->getDaysLeft()) ? -1 : 1;
            });
        }

        $user = new User();
        $user->setName($name);
        $user->setBooks($books);

        $this->client->request('GET', 'http://bibliotek.orebro.se/logout');

        return $user;
    }
}
