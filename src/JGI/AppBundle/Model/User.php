<?php

namespace JGI\AppBundle\Model;

class User {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Book[]
     */
    protected $books;

    public function __construct()
    {
        $this->books = [];
    }

    /**
     * @param \JGI\AppBundle\Model\Book[] $books
     */
    public function setBooks(array $books)
    {
        $this->books = $books;
    }

    /**
     * @return \JGI\AppBundle\Model\Book[]
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Book|null
     */
    public function getNextBookToReturn()
    {
        if (!$books = $this->getBooks()) {
            return null;
        }
        usort($books, function(Book $a, Book $b) {
            if ($a->getDaysLeft() == $b->getDaysLeft()) {
                return 0;
            }
            return ($a->getDaysLeft() < $b->getDaysLeft()) ? -1 : 1;
        });

        return current($books);
    }
}
