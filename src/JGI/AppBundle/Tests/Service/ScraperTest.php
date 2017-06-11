<?php

namespace JGI\AppBundle\Tests;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use JGI\AppBundle\Service\Scraper;
use Symfony\Component\DomCrawler\Form;

class ScraperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnListOfLateBooks()
    {
        $clientMock = $this->createMock(Client::class);

        $crawler1Mock = $this->createMock(Crawler::class);
        $clientMock->expects($this->at(0))->method('request')->willReturn($crawler1Mock);
        $clientMock->method('submit');

        $buttonCrawler = $this->createMock(Crawler::class);
        $buttonCrawler->method('form')->willReturn($this->createMock(Form::class));
        $crawler1Mock->method('selectButton')->willReturn($buttonCrawler);

        $crawler2 = new Crawler();
        $crawler2->addHtmlContent(file_get_contents(__DIR__ . '/loans.html'));

        $clientMock->expects($this->at(2))->method('request')->willReturn($crawler2);

        $scraper = new Scraper($clientMock);

        $user = $scraper->scrape('foo', 'bar', 'baz');

        $this->assertEquals('foo', $user->getName());
        $this->assertEquals(10, count($user->getBooks()));
        $this->assertEquals('2017-08-22', $user->getBooks()[0]->getReturnDate()->format('Y-m-d'));
        $this->assertEquals('Berättelser om en (inte så glamorös) TV-stjärna', $user->getBooks()[0]->getTitle());
        $this->assertEquals('Russell, Rachel Renée', $user->getBooks()[0]->getAuthor());
    }
}
