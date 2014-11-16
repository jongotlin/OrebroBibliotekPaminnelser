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
        $this->client->submit($form, ['username' => $username, 'password' => $password]);

        $crawler = $this->client->request('GET', 'http://bibliotek.orebro.se/my-pages/loans');

        $books = $crawler->filter('#loans tbody tr')->each(function(Crawler $node) {
            $book = new Book();
            if ($node->filter('td a')->count()) {
                $book->setTitle(trim($node->filter('td a')->text()));
            }
            if ($node->filter('td span.work-author')->count()) {
                $book->setAuthor(substr(trim($node->filter('td span.work-author')->text()), 4));
            }
            $book->setReturnDate(new \DateTime(trim($node->filter('td:nth-child(2)')->text())));

            return $book;
        });

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
